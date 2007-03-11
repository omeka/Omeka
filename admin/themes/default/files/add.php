<form method="post" enctype="multipart/form-data">
<!-- MAX_FILE_SIZE must precede the file input field -->
<input type="hidden" name="MAX_FILE_SIZE" value="30000000" />

<input name="itemfile[0]" type="file" />
<input type="submit" name="submit" value="Add this file" />
</form>