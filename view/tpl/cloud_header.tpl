<div class="section-title-wrapper">
	<div class="pull-right">
		<button id="files-create-btn"class="btn btn-xs btn-primary" title="{{if $quota.limit || $quota.used}}{{$quota.desc}}{{/if}}" onclick="openClose('files-mkdir-tools'); closeMenu('files-upload-tools');"><i class="icon-folder-close-alt"></i>&nbsp;Create</button>		
		<button id="files-upload-btn" class="btn btn-xs btn-success" title="{{if $quota.limit || $quota.used}}{{$quota.desc}}{{/if}}" onclick="openClose('files-upload-tools'); closeMenu('files-mkdir-tools');"><i class="icon-upload"></i>&nbsp;Upload</button>		
	</div>
	<h2>{{$header}}</h2>
		<div class="clear"></div>
</div>
{{$actionspanel}}

