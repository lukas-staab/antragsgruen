<?php

namespace app\controllers\admin;

use app\components\{Tools, ZipWriter};
use app\models\db\{Amendment, Consultation, IMotion, Motion, User};
use app\models\exceptions\ExceptionBase;
use app\models\forms\AdminMotionFilterForm;
use app\models\http\{BinaryFileResponse, HtmlErrorResponse, HtmlResponse, ResponseInterface};
use app\models\settings\{AntragsgruenApp, Privileges};
use app\views\amendment\LayoutHelper as AmendmentLayoutHelper;
use app\views\motion\LayoutHelper as MotionLayoutHelper;
use yii\web\Response;

class MotionListController extends AdminBase
{
    protected function actionListallScreeningMotions(): void
    {
        if ($this->isRequestSet('motionScreen')) {
            $motion = $this->consultation->getMotion($this->getRequestValue('motionScreen'));
            if (!$motion) {
                return;
            }
            $motion->setScreened();
            $this->getHttpSession()->setFlash('success', \Yii::t('admin', 'list_screened'));
        }
        if ($this->isRequestSet('motionUnscreen')) {
            $motion = $this->consultation->getMotion($this->getRequestValue('motionUnscreen'));
            if (!$motion) {
                return;
            }
            $motion->setUnscreened();
            $this->getHttpSession()->setFlash('success', \Yii::t('admin', 'list_unscreened'));
        }
        if ($this->isRequestSet('motionDelete')) {
            $motion = $this->consultation->getMotion($this->getRequestValue('motionDelete'));
            if (!$motion) {
                return;
            }
            $motion->setDeleted();
            $this->getHttpSession()->setFlash('success', \Yii::t('admin', 'list_deleted'));
        }

        if (!$this->isRequestSet('motions') || !$this->isRequestSet('save')) {
            return;
        }
        if ($this->isRequestSet('screen')) {
            foreach ($this->getRequestValue('motions') as $motionId) {
                $motion = $this->consultation->getMotion($motionId);
                if (!$motion) {
                    continue;
                }
                $motion->setScreened();
            }
            $this->getHttpSession()->setFlash('success', \Yii::t('admin', 'list_screened_pl'));
        }

        if ($this->isRequestSet('unscreen')) {
            foreach ($this->getRequestValue('motions') as $motionId) {
                $motion = $this->consultation->getMotion($motionId);
                if (!$motion) {
                    continue;
                }
                $motion->setUnscreened();
            }
            $this->getHttpSession()->setFlash('success', \Yii::t('admin', 'list_unscreened_pl'));
        }

        if ($this->isRequestSet('delete')) {
            foreach ($this->getRequestValue('motions') as $motionId) {
                $motion = $this->consultation->getMotion($motionId);
                if (!$motion) {
                    continue;
                }
                $motion->setDeleted();
            }
            $this->getHttpSession()->setFlash('success', \Yii::t('admin', 'list_deleted_pl'));
        }
    }

    protected function actionListallScreeningAmendments(): void
    {
        if ($this->isRequestSet('amendmentScreen')) {
            $amendment = $this->consultation->getAmendment($this->getRequestValue('amendmentScreen'));
            if (!$amendment) {
                return;
            }
            $amendment->setScreened();
            $this->getHttpSession()->setFlash('success', \Yii::t('admin', 'list_am_screened'));
        }
        if ($this->isRequestSet('amendmentUnscreen')) {
            $amendment = $this->consultation->getAmendment($this->getRequestValue('amendmentUnscreen'));
            if (!$amendment) {
                return;
            }
            $amendment->setUnscreened();
            $this->getHttpSession()->setFlash('success', \Yii::t('admin', 'list_am_unscreened'));
        }
        if ($this->isRequestSet('amendmentDelete')) {
            $amendment = $this->consultation->getAmendment($this->getRequestValue('amendmentDelete'));
            if (!$amendment) {
                return;
            }
            $amendment->setDeleted();
            $this->getHttpSession()->setFlash('success', \Yii::t('admin', 'list_am_deleted'));
        }
        if (!$this->isRequestSet('amendments') || !$this->isRequestSet('save')) {
            return;
        }
        if ($this->isRequestSet('screen')) {
            foreach ($this->getRequestValue('amendments') as $amendmentId) {
                $amendment = $this->consultation->getAmendment($amendmentId);
                if (!$amendment) {
                    continue;
                }
                $amendment->setScreened();
            }
            $this->getHttpSession()->setFlash('success', \Yii::t('admin', 'list_am_screened_pl'));
        }

        if ($this->isRequestSet('unscreen')) {
            foreach ($this->getRequestValue('amendments') as $amendmentId) {
                $amendment = $this->consultation->getAmendment($amendmentId);
                if (!$amendment) {
                    continue;
                }
                $amendment->setUnscreened();
            }
            $this->getHttpSession()->setFlash('success', \Yii::t('admin', 'list_am_unscreened_pl'));
        }

        if ($this->isRequestSet('delete')) {
            foreach ($this->getRequestValue('amendments') as $amendmentId) {
                $amendment = $this->consultation->getAmendment($amendmentId);
                if (!$amendment) {
                    continue;
                }
                $amendment->setDeleted();
            }
            $this->getHttpSession()->setFlash('success', \Yii::t('admin', 'list_am_deleted_pl'));
        }
    }

    protected function actionListallProposalAmendments(): void
    {
        if ($this->isRequestSet('proposalVisible')) {
            foreach ($this->getRequestValue('amendments', []) as $amendmentId) {
                $amendment = $this->consultation->getAmendment($amendmentId);
                if (!$amendment) {
                    continue;
                }
                $amendment->setProposalPublished();
            }
            $this->getHttpSession()->setFlash('success', \Yii::t('admin', 'list_proposal_published_pl'));
        }
    }


    public function actionIndex(?string $motionId = null): ResponseInterface
    {
        $consultation       = $this->consultation;
        $privilegeScreening = User::havePrivilege($consultation, Privileges::PRIVILEGE_SCREENING, null);
        $privilegeProposals = User::havePrivilege($consultation, Privileges::PRIVILEGE_CHANGE_PROPOSALS, null);
        if (!($privilegeScreening || $privilegeProposals)) {
            return new HtmlErrorResponse(403, \Yii::t('admin', 'no_acccess'));
        }

        $this->activateFunctions();

        if ($motionId === null || $motionId === 'all') {
            $consultation->preloadAllMotionData(Consultation::PRELOAD_ONLY_AMENDMENTS);
        }

        if ($privilegeScreening) {
            $this->actionListallScreeningMotions();
            $this->actionListallScreeningAmendments();
        }
        if ($privilegeProposals) {
            $this->actionListallProposalAmendments();
        }

        if ($motionId !== null && $motionId !== 'all' && $consultation->getMotion($motionId) === null) {
            $motionId = null;
        }
        if ($motionId === null && $consultation->getSettings()->adminListFilerByMotion) {
            $search = new AdminMotionFilterForm($consultation, $consultation->motions, true, $privilegeScreening);
            return new HtmlResponse($this->render('motion_list', ['motions' => $consultation->motions, 'search' => $search]));
        }

        if ($motionId !== null && $motionId !== 'all') {
            $motions = [$consultation->getMotion($motionId)];
        } else {
            $motions = $consultation->motions;
        }

        $search = new AdminMotionFilterForm($consultation, $motions, true, $privilegeScreening);
        if ($this->isRequestSet('Search')) {
            $search->setAttributes($this->getRequestValue('Search'));
        }

        return new HtmlResponse($this->render('list_all', [
            'motionId'           => $motionId,
            'entries'            => $search->getSorted(),
            'search'             => $search,
            'privilegeScreening' => $privilegeScreening,
            'privilegeProposals' => $privilegeProposals,
        ]));
    }

    public function actionMotionOdslistall(): BinaryFileResponse
    {
        // @TODO: support filtering for motion types and withdrawn motions

        $ods = $this->renderPartial('ods_list_all', [
            'items' => $this->consultation->getAgendaWithIMotions(),
        ]);
        return new BinaryFileResponse(BinaryFileResponse::TYPE_ODS, $ods, true, 'motions');
    }

    public function actionMotionOdslist(int $motionTypeId, bool $textCombined = false, int $withdrawn = 0): ResponseInterface
    {
        $withdrawn    = ($withdrawn == 1);
        $motionTypeId = intval($motionTypeId);

        try {
            $motionType = $this->consultation->getMotionType($motionTypeId);
        } catch (ExceptionBase $e) {
            return new HtmlErrorResponse(404, $e->getMessage());
        }

        $imotions = [];
        foreach ($this->consultation->getVisibleIMotionsSorted($withdrawn) as $imotion) {
            if ($imotion->getMyMotionType()->id === $motionTypeId) {
                $imotions[] = $imotion;
            }
        }

        $filename = Tools::sanitizeFilename($motionType->titlePlural, false);
        $ods = $this->renderPartial('ods_list', [
            'imotions'     => $imotions,
            'textCombined' => $textCombined,
            'motionType'   => $motionType,
        ]);
        return new BinaryFileResponse(BinaryFileResponse::TYPE_ODS, $ods, true, $filename);
    }

    /**
     * @param int $motionTypeId
     * @param bool $textCombined
     * @param int $withdrawn
     *
     * @return string
     * @throws \Yii\base\ExitException
     */
    public function actionMotionExcellist($motionTypeId, $textCombined = false, $withdrawn = 0)
    {
        $motionTypeId = intval($motionTypeId);

        if (!AntragsgruenApp::hasPhpExcel()) {
            $this->showErrorpage(500, 'The Excel package has not been installed. ' .
                                             'To install it, execute "./composer.phar require phpoffice/phpexcel".');
            return '';
        }

        $withdrawn = ($withdrawn == 1);

        try {
            $motionType = $this->consultation->getMotionType($motionTypeId);
        } catch (ExceptionBase $e) {
            $this->showErrorpage(404, $e->getMessage());
            return '';
        }

        defined('PCLZIP_TEMPORARY_DIR') or define('PCLZIP_TEMPORARY_DIR', $this->getParams()->getTmpDir());

        $excelMime                   = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->getHttpResponse()->format = Response::FORMAT_RAW;
        $this->getHttpResponse()->headers->add('Content-Type', $excelMime);
        $this->getHttpResponse()->headers->add('Content-Disposition', 'attachment;filename=motions.xlsx');
        $this->getHttpResponse()->headers->add('Cache-Control', 'max-age=0');

        error_reporting(E_ALL & ~E_DEPRECATED); // PHPExcel ./. PHP 7

        $motions = [];
        foreach ($this->consultation->getVisibleIMotionsSorted($withdrawn) as $motion) {
            if (is_a($motion, Motion::class) && $motion->motionTypeId == $motionTypeId) {
                $motions[] = $motion;
            }
        }

        return $this->renderPartial('excel_list', [
            'motions'      => $motions,
            'textCombined' => $textCombined,
            'motionType'   => $motionType,
        ]);
    }

    public function actionMotionOpenslides(int $motionTypeId, int $version = 1): ResponseInterface
    {
        $motionTypeId = intval($motionTypeId);

        try {
            $motionType = $this->consultation->getMotionType($motionTypeId);
        } catch (ExceptionBase $e) {
            return new HtmlErrorResponse(404, $e->getMessage());
        }


        $filename = Tools::sanitizeFilename($motionType->titlePlural, false);

        $motions = [];
        foreach ($this->consultation->getVisibleIMotionsSorted(false) as $motion) {
            if ($motion->getMyMotionType()->id == $motionTypeId) {
                $motions[] = $motion;
            }
        }

        if ($version == 1) {
            $csv = $this->renderPartial('openslides1_list', [
                'motions' => $motions,
            ]);
        } else {
            $csv = $this->renderPartial('openslides2_list', [
                'motions' => $motions,
            ]);
        }
        return new BinaryFileResponse(BinaryFileResponse::TYPE_CSV, $csv, true, $filename);
    }

    public function actionMotionPdfziplist(int $motionTypeId = 0, int $withdrawn = 0): ResponseInterface
    {
        $withdrawn    = ($withdrawn == 1);
        $motionTypeId = intval($motionTypeId);

        try {
            if ($motionTypeId > 0) {
                $motions = $this->consultation->getMotionType($motionTypeId)->getVisibleMotions($withdrawn);
            } else {
                $motions = $this->consultation->getVisibleMotions($withdrawn);
            }
            if (count($motions) === 0) {
                return new HtmlErrorResponse(404, \Yii::t('motion', 'none_yet'));
            }
            /** @var IMotion[] $imotions */
            $imotions = [];
            foreach ($motions as $motion) {
                if ($motion->getMyMotionType()->amendmentsOnly) {
                    $imotions = array_merge($imotions, $motion->getVisibleAmendments($withdrawn));
                } else {
                    $imotions[] = $motion;
                }
            }
        } catch (ExceptionBase $e) {
            return new HtmlErrorResponse(404, $e->getMessage());
        }

        $zip      = new ZipWriter();
        $hasLaTeX = ($this->getParams()->xelatexPath || $this->getParams()->lualatexPath);
        foreach ($imotions as $imotion) {
            if (is_a($imotion, Motion::class)) {
                if ($hasLaTeX && $imotion->getMyMotionType()->texTemplateId) {
                    $file = MotionLayoutHelper::createPdfLatex($imotion);
                    $zip->addFile($imotion->getFilenameBase(false) . '.pdf', $file);
                } elseif ($imotion->getMyMotionType()->getPDFLayoutClass()) {
                    $file = MotionLayoutHelper::createPdfTcpdf($imotion);
                    $zip->addFile($imotion->getFilenameBase(false) . '.pdf', $file);
                }
            } elseif (is_a($imotion, Amendment::class))  {
                if ($hasLaTeX && $imotion->getMyMotionType()->texTemplateId) {
                    $file = AmendmentLayoutHelper::createPdfLatex($imotion);
                    $zip->addFile($imotion->getFilenameBase(false) . '.pdf', $file);
                } elseif ($imotion->getMyMotionType()->getPDFLayoutClass()) {
                    $file = AmendmentLayoutHelper::createPdfTcpdf($imotion);
                    $zip->addFile($imotion->getFilenameBase(false) . '.pdf', $file);
                }
            }
        }

        return new BinaryFileResponse(BinaryFileResponse::TYPE_ZIP, $zip->getContentAndFlush(), true, 'motions_pdf');
    }

    public function actionMotionOdtziplist(int $motionTypeId = 0, int $withdrawn = 0): ResponseInterface
    {
        $withdrawn    = ($withdrawn == 1);
        $motionTypeId = intval($motionTypeId);

        try {
            if ($motionTypeId > 0) {
                $motions = $this->consultation->getMotionType($motionTypeId)->getVisibleMotions($withdrawn);
            } else {
                $motions = $this->consultation->getVisibleMotions($withdrawn);
            }
            if (count($motions) === 0) {
                return new HtmlErrorResponse(404, \Yii::t('motion', 'none_yet'));
            }
            /** @var IMotion[] $imotions */
            $imotions = [];
            foreach ($motions as $motion) {
                if ($motion->getMyMotionType()->amendmentsOnly) {
                    $imotions = array_merge($imotions, $motion->getVisibleAmendments($withdrawn));
                } else {
                    $imotions[] = $motion;
                }
            }
        } catch (ExceptionBase $e) {
            return new HtmlErrorResponse(404, $e->getMessage());
        }

        $zip = new ZipWriter();
        foreach ($imotions as $imotion) {
            if (is_a($imotion, Motion::class)) {
                $content = $this->renderPartial('@app/views/motion/view_odt', ['motion' => $imotion]);
                $zip->addFile($imotion->getFilenameBase(false) . '.odt', $content);
            }
            if (is_a($imotion, Amendment::class)) {
                $content = $this->renderPartial('@app/views/amendment/view_odt', ['amendment' => $imotion]);
                $zip->addFile($imotion->getFilenameBase(false) . '.odt', $content);
            }
        }

        return new BinaryFileResponse(BinaryFileResponse::TYPE_ZIP, $zip->getContentAndFlush(), true, 'motions_odt');
    }
}
