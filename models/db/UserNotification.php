<?php

namespace app\models\db;

use app\models\notifications\{CommentNotificationSubscriptions, MotionNotificationSubscriptions};
use app\models\settings\AntragsgruenApp;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $userId
 * @property int $consultationId
 * @property int $notificationType
 * @property int $notificationReferenceId
 * @property string|null $settings
 * @property string $lastNotification
 *
 * @property Consultation $consultation
 * @property User $user
 */
class UserNotification extends ActiveRecord
{
    public const NOTIFICATION_NEW_MOTION          = 0;
    public const NOTIFICATION_NEW_AMENDMENT       = 1;
    public const NOTIFICATION_NEW_COMMENT         = 2;
    public const NOTIFICATION_AMENDMENT_MY_MOTION = 3;

    public const COMMENT_REPLIES             = 0;
    public const COMMENT_SAME_MOTIONS        = 1;
    public const COMMENT_ALL_IN_CONSULTATION = 2;
    public const COMMENT_SETTINGS = [1, 0, 2]; // First value defines the default value

    public static function tableName(): string
    {
        return AntragsgruenApp::getInstance()->tablePrefix . 'userNotification';
    }

    public function getConsultation(): ActiveQuery
    {
        return $this->hasOne(Consultation::class, ['id' => 'consultationId']);
    }

    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'userId'])
            ->andWhere(User::tableName() . '.status != ' . User::STATUS_DELETED);
    }

    public function rules(): array
    {
        return [
            [['userId', 'consultationId', 'notificationType'], 'required'],
            [['id', 'userId', 'consultationId', 'notificationType', 'notificationReferenceId'], 'number'],
        ];
    }

    /**
     * @param mixed $default
     * @return null|mixed
     */
    public function getSettingByKey(string $key, $default = null)
    {
        if ($this->settings) {
            $settings = json_decode($this->settings, true);
            if (isset($settings[$key])) {
                return $settings[$key];
            }
        }
        return $default;
    }

    /**
     * @param mixed $value
     */
    public function setSettingByKey(string $key, $value)
    {
        $settings = [];
        if ($this->settings) {
            $settings = json_decode($this->settings, true);
        }
        $settings[$key] = $value;
        $this->settings = json_encode($settings);
    }

    /**
     * @param Consultation $consultation
     * @param null|int $notiType
     * @return UserNotification[]
     */
    public static function getConsultationNotifications(Consultation $consultation, $notiType = null)
    {
        if ($notiType) {
            $notifications = [];
            foreach ($consultation->userNotifications as $userNotification) {
                if ($userNotification->notificationType == $notiType) {
                    $notifications[] = $userNotification;
                }
            }
            return $notifications;
        } else {
            return $consultation->userNotifications;
        }
    }

    /**
     * @param User $user
     * @param Consultation $consultation
     * @return static[]
     */
    public static function getUserConsultationNotis(User $user, Consultation $consultation)
    {
        return static::findAll([
            'userId'         => $user->id,
            'consultationId' => $consultation->id,
        ]);
    }

    /** @var UserNotification[] */
    protected static array $noticache = [];

    /**
     * @param int $type
     * @param int|null $refId
     */
    public static function getNotification(User $user, Consultation $consultation, $type, $refId = null): ?UserNotification
    {
        $key = $user->id . '-' . $consultation->id . '-' . $type . '-' . $refId;
        if (!array_key_exists($key, static::$noticache)) {
            static::$noticache[$key] = static::findOne([
                'userId'                  => $user->id,
                'consultationId'          => $consultation->id,
                'notificationType'        => $type,
                'notificationReferenceId' => $refId,
            ]);
        }
        return static::$noticache[$key];
    }

    /**
     * @param int|null $refId
     */
    public static function addNotification(User $user, Consultation $consultation, $type, $refId = null): UserNotification
    {
        $noti = static::getNotification($user, $consultation, $type, $refId);
        if (!$noti) {
            $noti                          = new UserNotification();
            $noti->consultationId          = $consultation->id;
            $noti->userId                  = $user->id;
            $noti->notificationType        = $type;
            $noti->notificationReferenceId = $refId;
            $noti->save();
        }

        static::$noticache = [];

        return $noti;
    }

    /**
     * @param int $commentSetting
     */
    public static function addCommentNotification(User $user, Consultation $consultation, $commentSetting): void
    {
        if (!in_array($commentSetting, static::COMMENT_SETTINGS)) {
            return;
        }
        $noti = static::addNotification($user, $consultation, static::NOTIFICATION_NEW_COMMENT);
        $noti->setSettingByKey('comments', $commentSetting);
        $noti->save();
    }

    /**
     * @param int $type
     * @param int|null $refId
     */
    public static function removeNotification(User $user, Consultation $consultation, $type, $refId = null): void
    {
        $noti = static::getNotification($user, $consultation, $type, $refId);
        if ($noti) {
            $noti->delete();
        }
        static::$noticache = [];
    }

    public static function notifyNewMotion(Motion $motion): void
    {
        $notificationType = UserNotification::NOTIFICATION_NEW_MOTION;
        $notified         = [];
        foreach ($motion->getMyConsultation()->userNotifications as $noti) {
            if ($noti->notificationType === $notificationType && !in_array($noti->userId, $notified) && $noti->user) {
                new MotionNotificationSubscriptions($motion, $noti->user);

                $notified[]             = $noti->userId;
                $noti->lastNotification = date('Y-m-d H:i:s');
                $noti->save();
            }
        }
    }

    public static function notifyNewComment(IComment $comment): void
    {
        $usersRepliedTo = $comment->getUserIdsBeingRepliedToByThis();
        $usersInSameIMotion = $comment->getUserIdsActiveOnThisIMotion();

        $notificationType = UserNotification::NOTIFICATION_NEW_COMMENT;
        $notified = [];
        foreach ($comment->getConsultation()->userNotifications as $noti) {
            if ($noti->userId === $comment->userId) {
                continue;
            }
            if ($noti->notificationType === $notificationType && !in_array($noti->userId, $notified) && $noti->user) {
                $commentSetting = $noti->getSettingByKey('comments', static::COMMENT_SETTINGS[0]);
                if ($commentSetting === static::COMMENT_SAME_MOTIONS && !in_array($noti->userId, $usersInSameIMotion)) {
                    continue;
                }
                if ($commentSetting === static::COMMENT_REPLIES && !in_array($noti->userId, $usersRepliedTo)) {
                    continue;
                }
                // static::COMMENT_ALL_IN_CONSULTATION => all users get it

                new CommentNotificationSubscriptions($noti->user, $comment);

                $notified[] = $noti->userId;
                $noti->lastNotification = date('Y-m-d H:i:s');
                $noti->save();
            }
        }
    }
}
