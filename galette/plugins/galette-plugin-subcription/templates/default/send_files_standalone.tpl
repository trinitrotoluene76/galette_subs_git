{if $deleteok eq 1}
   <div id="successbox">{_T string="File deleted"}</div> 
{/if}
{if $deleteok eq 0}
   <div id="errorbox">{_T string="Your file hasn't been found"}</div> 
{/if}


	<form action="send_files_standalone.php?id_act={$file->id_act}{if $file->vierge eq 1}&vierge=1{/if}{if $file->vierge eq 0}&vierge=0{/if}&timestamp={$timestamp}{if $file->id_abn != NULL}&id_abn={$file->id_abn}{/if}{if $file->id_adh != NULL}&id_adh={$file->id_adh}{/if}" method="post" enctype="multipart/form-data" id="form">
			
	<div class="bigtable wrmenu">
		<table class="details">
			<caption class="ui-state-active ui-corner-top">{_T string="Add file by fill up this panel for the group:"} {$activity->group_name}</caption>
			
			<tr>
				<th>{_T string="1. Send your file"}</th>
				<td>
					<script language="JavaScript" type="text/javascript">
						<!--
						if (AC_FL_RunContent == 0 || DetectFlashVer == 0) 
							{
								alert("Cette page necessite le fichier AC_RunActiveContent.js.");
							} 
						else 
							{
							var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
							if(hasRightVersion) 
								{  // si nous avons détecté une version acceptable
								// intégrer le clip Flash
								AC_FL_RunContent(
									'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0',
									'width', '347',
									'height', '73',
									'src', 'formulaire',
									'quality', 'high',
									'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
									'align', 'middle',
									'play', 'true',
									'loop', 'true',
									'scale', 'showall',
									'wmode', 'window',
									'devicefont', 'false',
									'id', 'formulaire',
									'bgcolor', '#eeeeee',
									'name', 'formulaire',
									'menu', 'true',
									'allowScriptAccess','sameDomain',
									'allowFullScreen','false',
									'movie', '{$galette_base_path}{$subscription_dir}upload/code/formulaire',
									'salign', '',
									'flashvars', 'id_act={$file->id_act}&id_adh={$file->id_adh}&id_anim={$timestamp}_{$file->id_act}_1&cheminscriptreceveur=../../upload_process.php&chemindossier_files_abs={$galette_base_path}{$subscription_dir}upload/files&chemindossierupload=upload/files'
									); //end AC code
								} 
							else 
								{  // version Flash trop ancienne ou détection du plug-in impossible
								var alternateContent = '{_T string="We detect that your web browser doesn&#39;t have flash player. You need flash 8 minimum or use Chrome in portable version to upload files"}'
									+ '<div><img src="{$galette_base_path}{$subscription_tpl_dir}/images/chrome.png" width=25px height=25px><a target="blank" href="http://www.macromedia.com/go/getflash/" >{_T string=" (Get Flash player) "}<img src="{$galette_base_path}{$subscription_tpl_dir}/images/flash.jpg"  width=25px height=25px></a></div>';
								document.write(alternateContent);  // Insérer contenu non-Flash
								}
						}
						// -->
						</script>
						<noscript>
							{_T string="We detect that your web browser doesn't accept javascript, please configure it to accept this. Then, you are permitted to upload files"}
						 </noscript>
					 </td>
			
			
			
			<tr>
				<th>{_T string="2. Add a description"}</th>
				<td>
					<TEXTAREA NAME="description" ROWS=2 COLS=50></TEXTAREA>
				</td>
			</tr>
		{if $file->vierge eq 1}
			<tr>
				<th>{_T string="3. Form to fill up?"}</th>
				<td>
					<select name="return_file">
						<option name="0"  value="0">{_T string="no"}
						<option name="1" value="1">{_T string="yes"}
					</select>
					
				</td>
			</tr>
		{/if}	
		</table>
	</div>
				
			<div class="button-container">
				<a id="prev" class="button" href="" onclick="javascript:window.close()">{_T string="Previous"}</a>
				<input type="submit" name="valid" id="btnsave" value="{_T string="Save"}"/>
				<input type="hidden" name="id_group" value=""/>
				<input type="hidden" name="id_form" value=""/>
			</div>
	</form> 
	
	</BR>
	
{if $files_vierges> 0}
<b><u>{_T string="Files of group:"} {$activity->group_name}</b></u>
	<div class="bigtable wrmenu" >
        <table class="listing adherents" >
            <thead>
               <tr>
                    <th class="left date_row">
						{_T string="Link"}
                    </th>
                    <th class="left date_row">
						{_T string="Description"}
                    </th>
                    
					<th class="left date_row">
                        {_T string="Form to fill up?"}
                    </th>
                {if $file->vierge eq 1}
                    <th class="left">
                        {_T string="Delete"}
                    </th>
				{/if}
                </tr>
            </thead>
			
				
	{foreach from=$files_vierges key=k item=file2}
										
				<tr>
					<td>
						<a href="{$galette_base_path}{$subscription_dir}upload/files/{$file2->emplacement|escape}" title="{$file2->description|escape}" target="blank">{$file2->doc_name|escape} </a>
					</td>
					<td>
						{$file2->description|escape}
					</td>
					<td>
						{if $file2->return_file == 0}
						{_T string="No"}
						{/if}
						{if $file2->return_file == 1}
							<b>{_T string="Yes"}</b>
						{/if}
					</td>
				{if $file->vierge eq 1}
					<td>
						<a id="histreset" class="button" href="{$galette_base_path}{$subscription_dir}send_files_standalone.php?id_act={$file2->id_act}{if $file2->vierge eq 1}&vierge=1{/if}{if $file2->vierge eq 0}&vierge=0{/if}&delete=1&id_doc={$file2->id_doc}{if $file->id_abn != NULL}&id_abn={$file->id_abn}{/if}{if $file->id_adh != NULL}&id_adh={$file->id_adh}{/if}">{_T string="Delete"}</a>
					</td>
				{/if}
				</tr>
	{/foreach}			
				
        </table>
</div>
{/if}

</BR>
	
{if $personnal_files> 0 && $vierge == 0}
<b><u>{_T string="Personnal files of group:"} {$activity->group_name}</u></b>
	<div class="bigtable wrmenu" >
        <table class="listing adherents" >
            <thead>
               <tr>
                    <th class="left date_row">
						{_T string="Link"}
                    </th>
                    <th class="left date_row">
						{_T string="Description"}
                    </th>
                                        
                    <th class="left">
                        {_T string="Date"}
                    </th>
					 <th class="left">
                        {_T string="Subscription"}
                    </th>
					<th class="left">
                        {_T string="Delete"}
                    </th>
                </tr>
            </thead>
			
				
	{foreach from=$personnal_files key=k item=file}
										
				<tr>
					<td>
						<a href="{$galette_base_path}{$subscription_dir}upload/files/{$file->emplacement|escape}" title="{$file->description|escape}" target="blank">{$file->doc_name|escape} </a>
					</td>
					<td>
						{$file->description|escape}
					</td>
					<td>
						{$file->date_record|escape}
					</td>
					<td>
						{$file->id_abn}
					</td>
					
					<td>
						<a id="histreset" class="button" href="{$galette_base_path}{$subscription_dir}send_files_standalone.php?id_act={$file->id_act}{if $file->vierge eq 1}&vierge=1{/if}{if $file->vierge eq 0}&vierge=0{/if}&delete=1&id_doc={$file->id_doc}{if $file->id_abn != NULL}&id_abn={$file->id_abn}{/if}{if $file->id_adh != NULL}&id_adh={$file->id_adh}{/if}">{_T string="Delete"}</a>
					</td>
				</tr>
	{/foreach}			
				
        </table>
</div>
{/if}	
	
