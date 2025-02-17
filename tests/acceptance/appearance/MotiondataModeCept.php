<?php

/** @var \Codeception\Scenario $scenario */
$I = new AcceptanceTester($scenario);
$I->populateDBData1();

$I->gotoMotion();

$I->seeElement('.motionDataTable');
$I->see('Testuser', '.motionDataTable'); // Proposer
$I->see('Test2', '.motionDataTable'); // Consultation

$I->wantTo('disable the motion data');
$I->loginAsStdAdmin();
$page = $I->gotoStdAdminPage()->gotoAppearance();
$I->selectOption('#motiondataMode', \app\models\settings\Consultation::MOTIONDATA_NONE);
$page->saveForm();

$I->gotoMotion();

$I->dontSeeElement('.motionDataTable');

$I->wantTo('switch to mini-mode');
$page = $I->gotoStdAdminPage()->gotoAppearance();
$I->selectOption('#motiondataMode', \app\models\settings\Consultation::MOTIONDATA_MINI);
$page->saveForm();

$I->gotoMotion();
$I->seeElement('.motionDataTable');
$I->see('Testuser', '.motionDataTable'); // Proposer
$I->dontSee('Test2', '.motionDataTable'); // Consultation
