<div class="ewPager">
<?php if ($ewpp_TotalRecs > 0) { ?>
<?php if ($ewpp_TotalPages > 1) { ?>
<div class="ewPagination">
<ul class="pagination pagination-sm">
	<?php if ($ewpp_PageNumber > 1) { ?>
	<li><a href="<?php echo EWPP_CART_LIST_PAGE . "?" . EWPP_START_REC ?>=1"><?php echo $PPLanguage->Phrase("First") ?></a></li>
	<?php } else { ?>
	<li class="disabled"><a href="#"><?php echo $PPLanguage->Phrase("First") ?></a></li>
	<?php } ?>
	<?php if ($ewpp_PageNumber > 1) { ?>
	<li><a href="<?php echo EWPP_CART_LIST_PAGE . "?" . EWPP_START_REC . "=" . (($ewpp_PageNumber-2)*$ewpp_DisplayRecs+1) ?>"><?php echo $PPLanguage->Phrase("Prev") ?></a></li>
	<?php } else { ?>
	<li class="disabled"><a href="#"><?php echo $PPLanguage->Phrase("Prev") ?></a></li>
	<?php } ?>
	<?php for ($ewpp_PageCount = 1; $ewpp_PageCount <= $ewpp_TotalPages; $ewpp_PageCount++) { ?>
		<?php if ($ewpp_PageCount <> $ewpp_PageNumber) { ?>
			<li><a href="<?php echo EWPP_CART_LIST_PAGE . "?" . EWPP_START_REC . "=" . (($ewpp_PageCount-1)*$ewpp_DisplayRecs+1) ?>"><?php echo $ewpp_PageCount ?></a></li>
		<?php } else { ?>
			<li class="disabled"><a href="#"><?php echo $ewpp_PageCount ?></a></li>	
		<?php } ?>
	<?php } ?>
	<?php if ($ewpp_TotalPages > $ewpp_PageNumber) { ?>
	<li><a href="<?php echo EWPP_CART_LIST_PAGE . "?" . EWPP_START_REC . "=" . ($ewpp_PageNumber*$ewpp_DisplayRecs+1) ?>"><?php echo $PPLanguage->Phrase("Next") ?></a></li>
	<?php } else { ?>
	<li class="disabled"><a href="#"><?php echo $PPLanguage->Phrase("Next") ?></a></li>
	<?php } ?>
	<?php if ($ewpp_TotalPages > $ewpp_PageNumber) { ?>
	<li><a href="<?php echo EWPP_CART_LIST_PAGE . "?" . EWPP_START_REC . "=" . (($ewpp_TotalPages-1)*$ewpp_DisplayRecs+1) ?>"><?php echo $PPLanguage->Phrase("Last") ?></a></li>
	<?php } else { ?>
	<li class="disabled"><a href="#"><?php echo $PPLanguage->Phrase("Last") ?></a></li>
	<?php } ?>
</ul>
</div>	
<?php } ?>
	<div class="ewRec"><?php echo $PPLanguage->Phrase("Record") ?> <?php echo $ewpp_StartRec ?> <?php echo $PPLanguage->Phrase("To") ?> <?php echo $ewpp_StopRec ?> <?php echo $PPLanguage->Phrase("Of") ?> <?php echo $ewpp_TotalRecs ?></div>
	<div class="clearfix"></div>
<?php } else { ?>
	<?php if (!$ewpp_NoRecordShown) { ?>
<div class="ewStdTable"><?php echo $PPLanguage->Phrase("NoRecord") ?></div>
	<?php $ewpp_NoRecordShown = TRUE; ?>
	<?php } ?>
<?php } ?>
</div>
