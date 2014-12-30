/**
 * Module JS de CheckVersion pour backoffice
 * Plateforme AIRWEB Php
 * Necessite jQuery (Selector, Ajax...)
 */

__cv_reloadCheckVersion = null;


//Methode d'appel de checkversion
function __cv_loadCheckVersion(btnid, titrezone, leftcolumnzone, rightcolumnzone){
    __cv_reloadCheckVersion = function(){
	__cv_loadCheckVersion(btnid, titrezone, leftcolumnzone, rightcolumnzone);
    }
    $('.selected-menu-btn').removeClass('selected-menu-btn');
    $("#"+btnid).addClass('selected-menu-btn');
    $("#"+titrezone).html("<h2>Gestion des versions d'Application</h2>");
    $("#"+leftcolumnzone).html("");
    $("#"+rightcolumnzone).html("Chargement...");
    
    $.ajax({
	url:'js/mod_checkversion/ajax_cv.php',
	type:'post',
	data:{
	    type:'getAppVersions'
	},
	dataType:'json'
    })
    .success(function(data){
	__cv_showAppTable(data.appInfos, data.versions, data.selector, rightcolumnzone);
    })
    .error(function(a, b){
	console.log(a, b);
    });
}

function __cv_showAppTable(appInfos, versions, selector, rightcolumnzone){
    var res = "";
    requiredLabel = {'0':'Non','1':'Oui'};
    for (version in versions){
	res += '<h3>APPLICATION '+version+'</h3>';
	res += '<p>Etat du module de vérification de version pour '+version+' : ';
	if (appInfos[version].CV_ENABLED == 1){
	    res += '<label><input type="checkbox" checked="checked" class="cv-toggle-cv-module" data-version="'+version+'" />Activé</label>';
	}else{
	    res += '<label><input type="checkbox" class="cv-toggle-cv-module" data-version="'+version+'" />Activé</label>';
	}
	res += '<br/>';
	res += '<br/>';
	res += 'Lien vers le store : <a href="'+appInfos[version].APP_URL+'" style="color:#000;">'+appInfos[version].APP_URL+'</a>';
	res += '</p>';
	res += '<table class="data-table" id="data-table-version-'+version+'">';
	res += '<thead><tr><td>Numéro de version</td><td>Restriction</td><td>MAJ Obligatoire vers la dernière version</td><td>Action</td></tr></thead>';
	for (i=0;i<versions[version].length;i++){
	    res += '<tr id="cv-tr-'+version+'-'+versions[version][i].appVersion.replace(".", "-")+'"><td>'+versions[version][i].appVersion+'</td>';
	    if (versions[version][i].limitation == 'NONE'){
		res += '<td>Aucune</td>';
	    }else{
		res += '<td>'+versions[version][i].limitation+" "+versions[version][i].os_version_value+'</td>';
	    }
	    var isLast = "false";
	    if (i == (versions[version].length-1)){
		res += '<td>Dernière version</td>';
		isLast = "true";
	    }else{
		res += '<td>'+requiredLabel[versions[version][i].required]+'</td>';
	    }
	    
	    res += '<td><input type="button" class="button modifyCVButton" value="Modifier" data-version="'+version+'" data-app-version="'+versions[version][i].appVersion+'" data-app-is-last="'+isLast+'" data-i="'+i+'" /></td></tr>';
	}
	res += '</table>';
	res += '<input type="button" class="button addCVButton" value="Ajouter une version" data-version="'+version+'" />';
    }
    $("#"+rightcolumnzone).html(res);
    $('.button').button();
    $('.modifyCVButton').click(function(){
	__cv_modifyAppVersion($(this).attr('data-version'), $(this).attr('data-app-version'), versions, selector, $(this).attr('data-app-is-last'), $(this).attr('data-i'));
    });
    $('.addCVButton').click(function(){
	__cv_addAppVersion($(this).attr('data-version'), versions, selector);
    });
    $('.cv-toggle-cv-module').click(function(){
	if (confirm("Etes vous sûr de vouloir changer l'état du module de vérification de version?")){
	    var status;
	    if ($(this).is(':checked')){
		status = '1';
	    }else{
		status = '0';
	    }
	    $.ajax({
		url:'js/mod_checkversion/ajax_cv.php',
		type:'post',
		data:{
		    type:'updateCheckingVersionStatus',
		    status:status,
		    version:$(this).attr('data-version')
		},
		dataType:'json'
	    })
	    .success(function(){
		__cv_reloadCheckVersion();
	    })
	    .error(function(a, b){
		console.log(a, b);
	    });
	}else{
	    return false;
	}
    });
}

function __cv_modifyAppVersion(version, appversion, versions, selector, islast, i){
    var appversionid = appversion.replace(".", "-");
    $('.modifyCVButton').button({disabled:true});
    var res = "";
    var limitations = {"NONE":'Aucune', '>=':'≥', '<=':'≤', '>':'>', '<':'<', '=':'='};
    res += '<td><input type="text" id="cv-tr-version-'+version+'-'+appversionid+'" value="'+appversion+'" /></td>';
    res += '<td>';
    res += '<select id="cv-tr-limit-'+version+'-'+appversionid+'" >';
    for (var limitation in limitations){
	if (limitation == versions[version][i].limitation){
	    res += '<option value="'+limitation+'" selected="selected" >'+limitations[limitation]+'</option>';
	}else{
	    res += '<option value="'+limitation+'" >'+limitations[limitation]+'</option>';
	}
	
    }
    res += '</select>';
    if (versions[version][i].limitation == 'NONE'){
	res += ' <select id="cv-tr-select-'+version+'-'+appversionid+'" disabled="disabled" >';
    }else{
	res += ' <select id="cv-tr-select-'+version+'-'+appversionid+'" >';
    }
    for (var selected in selector[version]){
	if (selected == versions[version][i].os_version){
	    res += '<option value="'+selected+'" selected="selected">'+version+" "+selector[version][selected]+'</option>';
	}else{
	    res += '<option value="'+selected+'" >'+version+" "+selector[version][selected]+'</option>';
	}
	
    }
    res += '</select></td>';
    if (islast != "true"){
	if (versions[version][i].required == 1){
	    res += '<td><label><input type="checkbox" id="cv-tr-required-'+version+'-'+appversionid+'" checked="checked"/>Obligatoire</label></td>';
	}else{
	    res += '<td><label><input type="checkbox" id="cv-tr-required-'+version+'-'+appversionid+'" />Obligatoire</label></td>';
	}
    }else{
	res += '<td>Dernière version</td>';
    }
    
    
    res += '<td><input type="button" class="button" id="cv-tr-submit-'+version+'-'+appversionid+'" value="Valider" /><input type="button" class="button cvcancelbutton" value="Annuler" /></td>';
    
    $('#cv-tr-'+version+'-'+appversionid).html(res);
    
    $('.button').button();
    $('#cv-tr-submit-'+version+'-'+appversionid).click(function(){
	__cv_submitAppVersion(version, appversion);
    });
    $('#cv-tr-limit-'+version+'-'+appversionid).change(function(){
	if ($(this).val() == 'NONE'){
	    $('#cv-tr-select-'+version+'-'+appversionid).attr('disabled','disabled');
	}else{
	    $('#cv-tr-select-'+version+'-'+appversionid).removeAttr('disabled');
	}
    });
    $(".cvcancelbutton").click(function(){
	__cv_reloadCheckVersion();
    });
}

function __cv_addAppVersion(version, versions, selector){
    $('.modifyCVButton').button({disabled:true});
    var res = "";
    var limitations = {"NONE":'Aucune', '>=':'≥', '<=':'≤', '>':'>', '<':'<', '=':'='};
    res += '<td><input type="text" id="cv-tr-add-version-'+version+'" value="" /></td>';
    res += '<td>';
    res += '<select id="cv-tr-add-limit-'+version+'" >';
    for (var limitation in limitations){
	res += '<option value="'+limitation+'" >'+limitations[limitation]+'</option>';
    }
    res += '</select>';
    res += ' <select id="cv-tr-add-select-'+version+'" disabled="disabled" >';
    
    for (var selected in selector[version]){
	res += '<option value="'+selected+'" >'+version+" "+selector[version][selected]+'</option>';
    }
    res += '</select></td>';

    if (versions[version].required == 1){
	res += '<td><label><input type="checkbox" id="cv-tr-add-required-'+version+'" checked="checked"/>Obligatoire (ignoré si dernière version)</label></td>';
    }else{
	res += '<td><label><input type="checkbox" id="cv-tr-add-required-'+version+'" />Obligatoire (ignoré si dernière version)</label></td>';
    }
    
    res += '<td><input type="button" class="button" id="cv-tr-add-submit-'+version+'" value="Valider" /><input type="button" class="button cvcancelbutton" value="Annuler" /></td>';
    
    $('#data-table-version-'+version).append(res);
    
    $('.button').button();
    $('#cv-tr-add-submit-'+version).click(function(){
	__cv_submitAddAppVersion(version);
    });
    $('#cv-tr-add-limit-'+version).change(function(){
	if ($(this).val() == 'NONE'){
	    $('#cv-tr-add-select-'+version).attr('disabled','disabled');
	}else{
	    $('#cv-tr-add-select-'+version).removeAttr('disabled');
	}
    });
    $(".cvcancelbutton").click(function(){
	__cv_reloadCheckVersion();
    });
    
}

function __cv_submitAddAppVersion(version){ 
    var appVersion = $('#cv-tr-add-version-'+version).val();
    var limitation = $('#cv-tr-add-limit-'+version).val();
    var osVersion;
    if (limitation == 'NONE'){
	osVersion = 'NULL';
    }else{
	osVersion = $('#cv-tr-add-select-'+version).val();
    }
    var required;
    if ($('#cv-tr-add-required-'+version).length > 0){
	if ($('#cv-tr-add-required-'+version).is(':checked')){
	    required = 1;
	}else{
	    required = 0;
	}
    }else{
	required = 0;
    }

    $.ajax({
	url:'js/mod_checkversion/ajax_cv.php',
	type:'post',
	data:{
	    type:'addAppVersion',
	    version:version,
	    appVersion:appVersion,
	    limitation:limitation,
	    osVersion:osVersion,
	    required:required
	},
	dataType:'json'
    })
    .success(function(){
	__cv_reloadCheckVersion();
    })
    .error(function(a,b){
	console.log(a,b);
    });
}

function __cv_submitAppVersion(version, oldAppVersion){
    var oldAppVersionId = oldAppVersion.replace('.', '-');
    var appVersion = $('#cv-tr-version-'+version+'-'+oldAppVersionId).val();
    var limitation = $('#cv-tr-limit-'+version+'-'+oldAppVersionId).val();
    var osVersion;
    if (limitation == 'NONE'){
	osVersion = 'NULL';
    }else{
	osVersion = $('#cv-tr-select-'+version+'-'+oldAppVersionId).val();
    }
    var required;
    if ($('#cv-tr-required-'+version+'-'+oldAppVersionId).length > 0){
	if ($('#cv-tr-required-'+version+'-'+oldAppVersionId).is(':checked')){
	    required = 1;
	}else{
	    required = 0;
	}
    }else{
	required = 0;
    }

    $.ajax({
	url:'js/mod_checkversion/ajax_cv.php',
	type:'post',
	data:{
	    type:'updateAppVersion',
	    version:version,
	    appVersion:appVersion,
	    oldAppVersion:oldAppVersion,
	    limitation:limitation,
	    osVersion:osVersion,
	    required:required
	},
	dataType:'json'
    })
    .success(function(){
	__cv_reloadCheckVersion();
    })
    .error(function(a,b){
	console.log(a,b);
    });
    
}