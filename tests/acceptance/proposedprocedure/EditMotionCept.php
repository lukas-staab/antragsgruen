<?php

/** @var \Codeception\Scenario $scenario */

use app\models\db\IMotion;

$I = new AcceptanceTester($scenario);
$I->populateDBData1();

$I->gotoConsultationHome();
$I->loginAsProposalAdmin();
$I->gotoMotion(true, 'Testing_proposed_changes-630');

$I->dontSeeElement('#proposedChanges');
$I->clickJS('.proposedChangesOpener button');
$I->seeElement('#proposedChanges');
$I->dontSeeElement('#pp_section_2_0');


$I->wantTo('write internal comments');
$I->fillField('#proposedChanges .proposalCommentForm textarea', 'Internal comment!');
$I->executeJS('$("#proposedChanges .proposalCommentForm button").click();');
$I->wait(1);
$I->see('Internal comment!', '#proposedChanges .proposalCommentForm .commentList');


$I->wantTo('change the status to modified accepted');
$I->dontSeeCheckboxIsChecked('#proposedChanges .proposalStatus' . IMotion::STATUS_MODIFIED_ACCEPTED . ' input');
$I->dontSeeElement('#proposedChanges .status_' . IMotion::STATUS_MODIFIED_ACCEPTED);
$I->dontSeeElement('#proposedChanges .saving');
$I->executeJS('$("#proposedChanges .proposalStatus' . IMotion::STATUS_MODIFIED_ACCEPTED . ' input").prop("checked", true).change();');
$I->seeElement('#proposedChanges .status_' . IMotion::STATUS_MODIFIED_ACCEPTED);
$I->seeElement('#proposedChanges .saving');
$I->executeJS('$("#proposedChanges .saving button").click();');
$I->gotoMotion(true, 'Testing_proposed_changes-630');
$I->seeCheckboxIsChecked('#proposedChanges .proposalStatus' . IMotion::STATUS_MODIFIED_ACCEPTED . ' input');
$I->dontSeeElement('#proposedChanges .saving');


$I->wantTo('edit the modification');
$I->click('#proposedChanges .editModification');
$I->wait(1);
$I->dontSeeElement('.alert-success');
$I->see('Lorem ipsum dolor sit amet', '#section_holder_2');
$I->executeJS('CKEDITOR.instances.sections_2_wysiwyg.setData(CKEDITOR.instances.sections_2_wysiwyg.getData().replace(/Lorem ipsum dolor sit amet/, "Zombie ipsum dolor sit amet"))');
$I->submitForm('#proposedChangeTextForm', [], 'save');
$I->seeElement('.alert-success');
$I->wait(1);

$I->wantTo('make the proposal visible and notify the proposer of the amendment');
$I->executeJS('$("#proposedChanges input[name=proposalVisible]").prop("checked", true).change();');
$I->executeJS('$("#votingBlockId").val("NEW").trigger("change")');
$I->fillField('#newBlockTitle', 'Voting 1');
$I->clickJS('#proposedChanges .saving button');
$I->wait(1);
$I->see('Über den Vorschlag informieren und Bestätigung einholen', '#proposedChanges .notificationStatus');
$I->dontSeeElement('.notifyProposerSection');
$I->clickJS('#proposedChanges button.notifyProposer');
$I->wait(1);
$I->seeElement('.notifyProposerSection');
$I->clickJS('#proposedChanges button[name=notificationSubmit]');
$I->wait(1);
$I->see('Der/die Antragsteller*in wurde am');


$I->assertEquals('Voting 1', $I->executeJS('return $("#votingBlockId option:selected").text()'));


$I->wantTo('make the proposal page visible');
$I->gotoConsultationHome();
$I->logout();
$page = $I->loginAndGotoStdAdminPage()->gotoAppearance();
$I->checkOption('#proposalProcedurePage');
$page->saveForm();

$I->wantTo('see the proposal page');
$I->gotoConsultationHome();
$I->logout();
$I->seeElement('#proposedProcedureLink');
$I->click('#proposedProcedureLink');
$I->see('Voting 1', '.votingTable' . AcceptanceTester::FIRST_FREE_VOTING_BLOCK_ID);
$I->seeElement('.votingTable' . AcceptanceTester::FIRST_FREE_VOTING_BLOCK_ID . ' .motion118');


$I->wantTo('agree to the proposal');
$I->loginAsStdUser();
$I->gotoMotion(true, 'Testing_proposed_changes-630');
$I->see('Zombie', '#pp_section_2_0 ins');
$I->seeElement('.agreeToProposal');
$I->submitForm('.agreeToProposal', [], 'setProposalAgree');
$I->seeElement('.alert-success');


$I->wantTo('see the agreement as admin');
$I->logout();
$I->loginAsProposalAdmin();
$I->seeElement('.notificationSettings .accepted');

$I->markTestIncomplete('not developed from here on yet');

$I->wantTo('merge the amendment into the motion');
$I->gotoMotion(true, 'Testing_proposed_changes-630');
$I->see('Umwelt', '.motionDataTable');
$I->dontSeeElement('#sidebar .mergeamendments');

$I->logout();
$I->loginAsConsultationAdmin();
$I->click('#sidebar .mergeamendments a');
$I->seeCheckboxIsChecked('.amendment279 .textProposal input');
$I->dontSeeElement('.amendment280 .textProposal');
$I->uncheckOption('.amendment280 .colCheck input');
$I->submitForm('.mergeAllRow', []);
$I->wait(1);

$I->see('A really small replacement', '#sections_2_1_wysiwyg ins');
$I->executeJS('$(".none").remove();'); // for some reason necessary...
$I->executeJS('$("#draftSavingPanel").remove();'); // for some reason necessary...
$I->submitForm('.motionMergeForm', [], 'save');
$I->see('A really small replacement');
$I->dontSee('A big replacement');
$I->submitForm('#motionConfirmForm', [], 'confirm');
$I->submitForm('#motionConfirmedForm', []);
$I->see('A really small replacement');

$I->see('A8neu', 'h1');
$I->see('Umwelt', '.motionDataTable');
$I->gotoConsultationHome();
$I->see('A8neu');
$I->dontSeeElement('.motionRow118');
