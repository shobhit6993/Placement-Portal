<?php
/* @var $this PlacementRepController */
/* @var $data PlacementRep */
?>

<div class="view">
	<b><?php echo CHtml::encode("st_id"); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data['st_id']),array('company/viewStudDetails', 'st_id'=>$data['st_id'])); ?>
	<br />

	<b><?php echo CHtml::encode("roll_no"); ?>:</b>
	<?php echo CHtml::encode($data['roll_no']); ?>
	<br />

	<b><?php echo CHtml::encode("Student name"); ?>:</b>
	<?php echo CHtml::encode($data['name']); ?>
	<br />

	<b><?php echo CHtml::encode("c_id"); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data['c_id']),array('viewCompDetails', 'c_id'=>$data['c_id'])); ?>
	<br />

	<b><?php echo CHtml::encode("Company name"); ?>:</b>
	<?php echo CHtml::encode($data['cname']); ?>
	<br />

	<b><?php echo CHtml::encode("j_id"); ?>:</b>
	<?php echo CHtml::encode($data['j_id']); ?>
	<br />

	<b><?php echo CHtml::encode("Job description"); ?>:</b>
	<?php echo CHtml::encode($data['description']); ?>
	<br />

	<b><?php echo CHtml::encode("ctc"); ?>:</b>
	<?php echo CHtml::encode($data['ctc']); ?>
	<br />

	<b><?php echo CHtml::encode("cpi_cutoff"); ?>:</b>
	<?php echo CHtml::encode($data['cpi_cutoff']); ?>
	<br />

	<b><?php echo CHtml::encode("approved"); ?>:</b>
	<?php echo CHtml::encode($data['approved']); ?>
	<br />

	<b><?php echo CHtml::encode("deadline"); ?>:</b>
	<?php echo CHtml::encode($data['deadline']); ?>
	<br />

	<b><?php echo CHtml::encode("tstamp"); ?>:</b>
	<?php echo CHtml::encode($data['tstamp']); ?>
	<br />

    <b><?php echo CHtml::encode("CV Link"); ?>:</b>
    <?php echo CHtml::link(CHtml::encode("Link"),Yii::app()->getBaseUrl(true)."/".$data['cv']); ?>
    <br />



	<div class="row buttons">
		<?php echo CHtml::button('Give Offer', array('submit' => array('offer/create', 'st_id'=>$data['st_id'], 'c_id'=>$data['c_id'], 'j_id'=>$data['j_id']))); ?>
	</div>


	

</div>

