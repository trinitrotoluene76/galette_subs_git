 <p> 
{_T string="Form"} ->  <b><u>{_T string="Send files"}</u></b> -> {_T string="Validation"} -> {_T string="Payement"}
 </p>      

 </BR>
  {_T string="You've chosen the following activities:"}
 </BR></BR>
 <FORM NAME="form" action="subs_confirmation.php" method="post" enctype="multipart/form-data"> 
  <div class="bigtable wrmenu">
		{foreach from=$activities key=k item=activity}
		<table class="details">
			<caption class="ui-state-active ui-corner-top">{$activity->group_name|escape}</caption>
			<tr>
				<th colspan=2> {_T string="Add a message for the manager of activity"}</th>
				<td >
					<input type="hidden" name="id_group{$activity->id_group}" value="{$activity->id_group}"/>
					<TEXTAREA NAME="message_adh_act{$activity->id_group}" ROWS=2 COLS=20></TEXTAREA>					
				</td>
			</tr>
		{if $files_vierges[{$activity->id_group}] >0}
			<tr>
				<td colspan=3>----------------------------------------------------------------------------------------------------
				</td>
			</tr>
			<tr>
					<th colspan=3>{_T string="List of needed files"}
					</th>
					
			</tr>			
         
			<tr>
				<th>1. {_T string="File name to fill up"}</th>
				<th>2. {_T string="Upload your file filled up"}</th>
				<th>3. {_T string="Add informations about your file"}</th>
			</tr>
			
		{foreach from=$files_vierges[{$activity->id_group}] key=k2 item=file}	
			<tr>
				<td><a href="{$galette_base_path}{$subscription_dir}upload/files/{$file->emplacement}" title="{$file->description|escape}" target="blank">{$file->doc_name|escape}<div align="center"><img src="{$subcription_tpl_dir}templates/default/images/download.png" height="17px" widht="17px"></div></a></td>
			{if	$file->return_file == 1}
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
								'flashvars', 'id_act={$activity->id_group}&id_adh={$subscription->id_adh}&id_anim={$timestamp}_{$activity->id_group}_{$k2}&cheminscriptreceveur=../../upload_process.php&chemindossier_files_abs={$galette_base_path}{$subscription_dir}upload/files&chemindossierupload=upload/files'
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
				<td>
					<TEXTAREA NAME="description_{$activity->id_group}_{$k2}" ROWS=3 COLS=25></TEXTAREA>
					<input type="hidden" name="timestamp_{$activity->id_group}_{$k2}" value="{$timestamp}_{$activity->id_group}_{$k2}"/>
				</td>
			
			{else}
				<td>
				</td>
				<td>
				</td>
			{/if}
			
			</tr>
		{/foreach}
		{/if}	
		</table>
		{/foreach}
		
	</div>
  <div class="bigtable wrmenu">
		<table class="details">
			<caption class="ui-state-active ui-corner-top">{_T string="Subscription"}</caption>
			
			{if $picture == 0}
				<tr>
					<th>{_T string="You haven't got a picture on your profile. We need one for the licence"}</BR></th>
					<td>
						<a href="{$galette_base_path}voir_adherent.php" title="{_T string="Add picture on my profile"}" target="blank">{_T string="Add picture on my profile"}</a>
					</td>
				</tr>
			{/if}
			
			<tr>
				<th>{_T string="Add a message for subscription"}</th>
				<td>
					<TEXTAREA NAME="message_abn" ROWS=2 COLS=20></TEXTAREA>
				</td>
			</tr>
			<tr>
				<th>{_T string="Estimated TOTAL:"}</th>
				<td>
					{$subscription->total_estimme} &euro;
					<input type="hidden" name="total_estimme" value="{$subscription->total_estimme}"/>
				</td>
			</tr>
			
		</table>
	</div>
</BR>
 <div class="button-container">
	<a id="histreset" class="button" href="{$galette_base_path}voir_adherent.php">{_T string="Cancel my request"}</a>
	<input type="submit" name="next" id="next" value="{_T string="Send my request"}"/>
</FORM>
</div>