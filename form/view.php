<?php
/************************************************************
 * DESIGNERS: SCROLL DOWN! (IGNORE ALL THIS STUFF AT THE TOP)
 ************************************************************/
defined('C5_EXECUTE') or die("Access Denied.");
$survey = $controller;
$miniSurvey = new MiniSurvey($b);
$miniSurvey->frontEndMode = true;

//Clean up variables from controller so html is easier to work with...
$bID = intval($bID);
$qsID = intval($survey->questionSetId);
$formAction = $this->action('submit_form').'#'.$qsID;

$questionsRS = $miniSurvey->loadQuestions($qsID, $bID);
$questions = array();
while ($questionRow = $questionsRS->fetchRow()) {
	$question = $questionRow;
	$question['input'] = $miniSurvey->loadInputType($questionRow, false);
	
	//Make type names common-sensical
	if ($questionRow['inputType'] == 'text') {
		$question['type'] = 'textarea';
	} else if ($questionRow['inputType'] == 'field') {
		$question['type'] = 'text';
	} else {
		$question['type'] = $questionRow['inputType']; //checkboxlist, select, radios, fileupload
	}
	
	//Make radioList wrapper consistent with checkboxList wrapper
	if ($questionRow['inputType'] == 'radios') {
		$question['input'] = "<div class=\"radioList\">\n{$question['input']}</div>\n";
	}
	
	//Construct label "for" (and add id's to inputs so they work)
	$question['input'] = str_replace(' id=""', '', $question['input']); //clean up silly file elements which for some reason have an id attribute but it's always empty (if we left this in, it would mess up our code below that inserts an actual id)
	$hasIdAlready = (strpos($question['input'], ' id="') !== false); //sanity check (as of this writing, c5 doesn't give id's to any form fields -- but maybe in the future?)
	$canBeLabelled = in_array($question['type'], array('text', 'textarea', 'select', 'fileupload')); //checkboxlist and radios can't get labels because they have multiple inputs (ideally the text of each answer would be a label, but that's too much hackery for us to do safely here in the template)
	if (!$hasIdAlready && $canBeLabelled) {
		$domId = "form{$bID}_question{$questionRow['msqID']}";
		if ($question['type'] == 'text' || $question['type'] == 'fileupload') {
			$search = '<input';
			$replace = '<input id="'.$domId.'"';
			$question['input'] = str_replace($search, $replace, $question['input']);
		} else if ($question['type'] == 'textarea') {
			$search = '<textarea';
			$replace = '<textarea id="'.$domId.'"';
			$question['input'] = str_replace($search, $replace, $question['input']);
		} else if ($question['type'] == 'select') {
			$search = '<select';
			$replace = '<select id="'.$domId.'"';
			$question['input'] = str_replace($search, $replace, $question['input']);
		}
		$question['labelFor'] = " for=\"{$domId}\"";
	} else {
		$question['labelFor'] = '';
	}
	
	$questions[] = $question;
}

//Prep thank-you message
$success = ($_GET['surveySuccess'] && $_GET['qsid'] == intval($qsID));
$thanksMsg = $survey->thankyouMsg;

//Collate all errors and put them into divs
$errorHeader = $formResponse;
$errors = is_array($errors) ? $errors : array();
if ($invalidIP) {
	$errors[] = $invalidIP;
}
$errorDivs = '';
foreach ($errors as $error) {
	$errorDivs .= '<div class="error">'.$error."</div>\n"; //It's okay for this one thing to have the html here -- it can be identified in CSS via parent wrapper div (e.g. '.formblock .error')
}

//Prep captcha
$surveyBlockInfo = $miniSurvey->getMiniSurveyBlockInfoByQuestionId($qsID, $bID);
$captcha = $surveyBlockInfo['displayCaptcha'] ? Loader::helper('validation/captcha') : false;

//Localized labels
$translatedCaptchaLabel = t('Please type the letters and numbers shown in the image.');
$translatedSubmitLabel = t('Submit');

/******************************************************************************
* DESIGNERS: CUSTOMIZE THE FORM HTML STARTING HERE...
*/?>

<div id="formblock<?php echo $bID; ?>" class="formblock">
<form enctype="multipart/form-data" id="miniSurveyView<?php echo $bID; ?>" class="miniSurveyView" method="post" action="<?php echo $formAction ?>">

	<?php if ($success): ?>
		
		<div class="success">
			<?php echo $thanksMsg; ?>
		</div>
	
	<?php elseif ($errors): ?>

		<div class="errors">
			<?php echo $errorHeader; ?>
			<?php echo $errorDivs; /* each error wrapped in <div class="error">...</div> */ ?>
		</div>

	<?php endif; ?>


	<div class="fields">
		
		<?php foreach ($questions as $question): ?>
			<div class="field field-<?php echo $question['type']; ?>">
				<label <?php echo $question['labelFor']; ?>>
					<?php echo $question['question']; ?>
					<?php if ($question['required']): ?>
						<span class="required">*</span>
					<?php endif; ?>
				</label>
				
				<?php echo $question['input']; ?>
			</div>
		<?php endforeach; ?>
		
	</div><!-- .fields -->
	
	<?php if ($captcha): ?>
		<div class="captcha">
			<label><?php echo $translatedCaptchaLabel; ?></label>
			<br />
			<?php $captcha->display(); ?>
			<br />
			<?php $captcha->showInput(); ?>
		</div>
	<?php endif; ?>

	<input type="submit" name="Submit" class="submit" value="<?php echo $translatedSubmitLabel; ?>" />

	<input name="qsID" type="hidden" value="<?php echo $qsID; ?>" />
	<input name="pURI" type="hidden" value="<?php echo $pURI; ?>" />
	
</form>
</div><!-- .formblock -->
