var uploader = new plupload.Uploader({
	runtimes : 'html5,flash',
	containes : 'plupload',
	browse_button : 'browse',
	drop_element : "droparea",
	url : 'index.php',
	flash_swf_url : 'js/plupload/plupload.flash.swf',
	multipart : true,
	urlstream_upload : true,
	multipart_params : {directory : 'test'},
});

uploader.bind('Init',function(up, params){
	if(params.runtime != 'html5'){
		$('#droparea').css('border','none').find('p,span').remove();
	}
})

uploader.bind('UploadProgress',function(up, file){
	$('#'+file.id).find('.progress').css('width',file.percent+'%');
})

uploader.init();

uploader.bind('FilesAdded',function(up,files){
	var filelist = $('#filelist');
	console.log(files);
	for(var i in files) {
		var file = files[i];
		filelist.prepend('<div id="'+file.id+'" class="file">'+file.name+' ('+plupload.formatSize(file.size)+')'+'<div class="progressbar"><div class="progress"></div></div></div>');
	}
	$('#droparea').removeClass('hover')
	uploader.start();
	uploader.refresh();
});

uploader.bind('Error',function(up, err){
	alert(err.message);
	$('#droparea').removeClass('hover')
	uploader.refresh();
});

uploader.bind('FileUploaded',function(up, file, response){
	data = $.parseJSON(response.response);
	if (data.error){
		alert(data.message);
		$('#'+file.id).remove();
	} else {
		$('#'+file.id).replaceWith(data.html);
	}
});

jQuery(function($){
	$('#droparea').bind({
		dragover : function(e){
			$(this).addClass('hover');
		},
		dragleave : function(e){
			$(this).removeClass('hover');
		}
	});

	$('body').on('click', '.del', function(e){
		e.preventDefault();
		var elem = $(this);
		if(confirm('Voulez-vous vraiment supprimer cette image ?')){
			$.get('index.php',{action:'delete',file:elem.attr('href')});
			elem.parent().parent().slideUp();
		}
		return false;
	});
})