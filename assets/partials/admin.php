<h1>Store Importer</h1>

<form method="POST" autocomplete="off" enctype="multipart/form-data">
	<input type="file" name="si-csv"/>
	<button type="submit" name="si-import" class="button button-primary">Run import</button>
</form>

<hr/>

<p>CSV columns are expected to be:</p>
<ul style="list-style:initial;padding-left:1em;">
	<li>Account ID <strong>(required for identifying existing stores)</strong></li>
	<li>Account Name</li>
	<li>Shipping Street</li>
	<li>Shipping City</li>
	<li>Shipping State/Province</li>
	<li>Phone</li>
	<li>Fax</li>
	<li>Website</li>
</ul>