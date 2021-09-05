<?php

use app\components\UrlHelper;
use app\models\db\{Motion, User};
use app\models\proposedProcedure\Factory;
use yii\helpers\Html;

/**
 * @var \app\controllers\Base $controller
 * @var Motion|null $assignedToMotion
 */

$controller = $this->context;
$consultation = $controller->consultation;
$layout = $controller->layoutParams;

$votingBlocksToRender = Factory::getOpenVotingBlocks($consultation, $assignedToMotion);
if (count($votingBlocksToRender) === 0 && !Factory::hasOnlineVotingBlocks($consultation)) {
    // Hint: we poll once there is a online voting block created
    return;
}

$layout->loadVue();
$layout->addVueTemplate('@app/views/voting/voting-block.vue.php');

$apiData = [];
foreach ($votingBlocksToRender as $votingBlockToRender) {
    $apiData[] = $votingBlockToRender->getUserApiObject(User::getCurrentUser());
}

$assignedToMotionId = ($assignedToMotion ? $assignedToMotion->id : '');
$pollUrl   = UrlHelper::createUrl(['/voting/get-open-voting-blocks', 'assignedToMotionId' => $assignedToMotionId]);
$voteUrl   = UrlHelper::createUrl(['/voting/post-vote', 'votingBlockId' => 'VOTINGBLOCKID', 'assignedToMotionId' => $assignedToMotionId]);
?>
<section data-url-poll="<?= Html::encode($pollUrl) ?>"
         data-url-vote="<?= Html::encode($voteUrl) ?>"
         data-antragsgruen-widget="frontend/VotingBlock" class="currentVotingWidget votingCommon"
         data-voting="<?= Html::encode(json_encode($apiData)) ?>"
>
    <div class="currentVoting"></div>
</section>

