<!-- Arika template -->

<h2 id="reply-title" class="comment-reply-title">Upload new Subtitle</h2>
<form enctype="multipart/form-data" action="<?php echo Tools::genURI('upload', 'upload'); ?>" method="post" class="comment-form" novalidate>

<p class="comment-notes">Required fields are marked <span class="required">*</span></p>

<p class="comment-form-author"><label for="title">Subtitle name<span class="required">*</span></label> 
<input id="title" name="title" type="text" value="<?php echo $renderData['title']; ?>" size="30" aria-required='true' required='required' /></p>

<p class="comment-form-author"><label for="season">Season</label> 
<input id="season" name="season" type="text" value="<?php echo $renderData['season']; ?>" size="3" /></p>

<p class="comment-form-author"><label for="episode">Episode</label> 
<input id="episode" name="episode" type="text" value="<?php echo $renderData['episode']; ?>" size="3" /></p>

<p class="comment-form-author"><label for="original_language">Translate from<span class="required">*</span></label> 
	<select id="original_language" name="original_language">
	<?php Tools::genLanguages($renderData['original_language']); ?>
	</select>
</p>

<p class="comment-form-author"><label for="language">Translate to<span class="required">*</span></label> 
	<select id="language" name="language">
	<?php Tools::genLanguages($renderData['language']); ?>
	</select>
</p>

<p class="comment-form-author"><label for="framerate">Frame rate<span class="required">*</span></label> 
	<select id="framerate" name="framerate">
	<?php Tools::genFramerates($renderData['framerate']); ?>
	</select>
</p>

<p class="comment-form-author"><label for="status">Status<span class="required">*</span></label> 
	<select id="status" name="status">
	<?php Tools::genStatus($renderData['status']); ?>
	</select>
</p>

<p class="comment-form-comment"><label for="comment">Comments</label>
<textarea id="comment" name="comment" cols="45" rows="2"><?php echo $renderData['comment']; ?></textarea></p>

<p class="comment-form-author"><label for="subfile">Subtitle file (srt format)<span class="required">*</span></label>
<input id="subfile" name="subfile" type="file" /></p>

<p class="form-submit"><input name="submit" type="submit" id="submit" class="submit" value="Upload" /></p>
</form>

<!-- END Arika template -->