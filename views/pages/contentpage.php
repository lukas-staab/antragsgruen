<?php

/**
 * @var $this yii\web\View
 * @var string $pageKey
 * @var bool $admin
 */

use app\components\UrlHelper;
use app\models\db\{ConsultationText, User};
use yii\helpers\Html;

/** @var \app\controllers\ConsultationController $controller */
$controller = $this->context;

$consultation = UrlHelper::getCurrentConsultation();
$site         = ($consultation ? $consultation->site : null);
$pageData     = ConsultationText::getPageData($site, $consultation, $pageKey);
$saveUrl      = $pageData->getSaveUrl();

$this->title = $pageData->title ?: $pageData->textId;

$layout = $controller->layoutParams;
if ($controller->action->id !== 'home') {
    $layout->addBreadcrumb($pageData->breadcrumb ?: $pageData->textId);
} else {
    $layout->breadcrumbs = [];
}

if (User::getCurrentUser() && $pageData->isCustomPage()) {
    $layout->loadVue();
    $layout->addFullscreenTemplates();
    $fullscreenInitData = json_encode([
        'consultation_url' => UrlHelper::createUrl(['/consultation/rest']),
        'init_page' => 'page-' . $pageData->id,
        'init_content_url' => UrlHelper::absolutizeLink($pageData->getJsonUrl()),
    ]);
    $fullscreenButton = '<button type="button" title="' . Yii::t('motion', 'fullscreen') . '" class="btn btn-link btnFullscreen"
        data-antragsgruen-widget="frontend/FullscreenToggle" data-vue-element="fullscreen-projector" data-vue-initdata="' . Html::encode($fullscreenInitData) . '">
        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
        <span class="sr-only">' . Yii::t('motion', 'fullscreen') . '</span>
    </button>';
} else {
    $fullscreenButton = '';
}

echo '<h1 class="pageTitle">' . Html::encode($pageData->title ?: $pageData->textId) . $fullscreenButton . '</h1>';

if ($admin) {
    $layout->loadCKEditor();

    echo Html::beginForm($saveUrl, 'post', [
        'class'                    => 'contentEditForm',
        'data-upload-url'          => $pageData->getUploadUrl(),
        'data-image-browse-url'    => $pageData->getImageBrowseUrl(),
        'data-antragsgruen-widget' => 'frontend/ContentPageEdit',
    ]);

    if (!in_array($pageData->textId, array_keys(ConsultationText::getDefaultPages()))) {
        ?>
        <section class="contentSettingsToolbar toolbarBelowTitle row form-inline hidden">
            <div class="col-md-4 textfield">
                <div class="form-group">
                    <label for="contentUrl"><?= Yii::t('pages', 'settings_url') ?>:</label>
                    <input type="text" class="form-control" name="url" value="<?= Html::encode($pageData->textId) ?>"
                           required id="contentUrl">
                </div>
            </div>
            <div class="col-md-4 textfield">
                <div class="form-group">
                    <label for="contentTitle"><?= Yii::t('pages', 'settings_title') ?>:</label>
                    <input type="text" class="form-control" name="title" value="<?= Html::encode($pageData->title) ?>"
                           required id="contentTitle" maxlength="30">
                </div>
            </div>
            <div class="col-md-4 options">
                <label>
                    <?= Html::checkbox('allConsultations', ($pageData->consultationId === null)) ?>
                    <?= Yii::t('pages', 'settings_allcons') ?>
                </label>
                <label>
                    <?= Html::checkbox('inMenu', ($pageData->menuPosition !== null)) ?>
                    <?= Yii::t('pages', 'settings_inmenu') ?>
                </label>
            </div>
        </section>
        <?php
    }
}

echo '<div class="content contentPage">';

if ($admin) {
    echo '<a href="#" class="editCaller" style="float: right;">' . Yii::t('base', 'edit') . '</a><br>';
}

echo '<article class="textHolder" id="stdTextHolder">';
echo $pageData->text;
echo '</article>';

if ($admin) {
    echo '<div class="textSaver hidden">';
    echo '<button class="btn btn-primary submitBtn" type="submit">';
    echo Yii::t('base', 'save') . '</button></div>';
}

echo '</div>';

if ($admin) {
    echo Html::endForm();

    $deleteUrl = UrlHelper::createUrl(['pages/delete-page', 'pageSlug' => $pageData->textId]);
    echo Html::beginForm($deleteUrl, 'post', ['class' => 'deletePageForm']);
    echo '<input type="hidden" name="delete" value="delete">';
    echo '<button type="submit" class="btn btn-link btn-danger pull-right">';
    echo '<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> ' . Yii::t('pages', 'settings_delete');
    echo '</button>';
    echo Html::endForm();
}
