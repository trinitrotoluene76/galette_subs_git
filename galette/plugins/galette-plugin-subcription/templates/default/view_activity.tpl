	<div class="bigtable wrmenu">
		<table class="details">
			<caption class="ui-state-active ui-corner-top">{$activity->group_name|escape|nl2br}</caption>
			
			{if $picture == 0}
				{_T string="-> You haven't got a picture on your profile. We need one for the licence"}</BR>
				<a href="{$galette_base_path}voir_adherent.php" title="{_T string="Add picture on my profile"}" target="blank">{_T string="Add picture on my profile"}</a>
			{/if}
			<tr>
				<th>{_T string="Manager"}</th>
				<td>
					{if isset($managers[0])}
						{foreach from=$managers key=k2 item=bi}
							
							{$managers[$k2]->surname|escape|nl2br}, {$managers[$k2]->name|escape|nl2br}, {$managers[$k2]->phone|escape|nl2br}, <a href=mailto:{$managers[$k2]->email|escape|nl2br}>mail</a>
							</BR>
						{/foreach}
					
					{else}
						<a class="link" href="{$galette_base_path}gestion_groupes.php">{_T string="Nobody is manager, go to group management page"}</a>
					{/if}
				
				</td>
			</tr>
			
			<tr>
				<th>{_T string="Price 1"}</th>
				<td>
					{$activity->price1|escape|nl2br} &euro;
				</td>
			</tr>
			<tr>
				<th>{_T string="Price 2"}</th>
				<td>
					{$activity->price2|escape|nl2br} &euro;
				</td>
			</tr>
			<tr>
				<th>{_T string="Price 3"}</th>
				<td>
					{$activity->price3|escape|nl2br} &euro;
				</td>
			</tr>
			<tr>
				<th>{_T string="Price 4"}</th>
				<td>
					{$activity->price4|escape|nl2br} &euro;
				</td>
			</tr>
			<tr>
				<th>{_T string="Place"}</th>
				<td>
					{$activity->lieu|escape|nl2br}
				</td>
			</tr>
			<tr>
				<th>{_T string="Day"}</th>
				<td>
					{$activity->jours|escape|nl2br}
				</td>
			</tr>
			<tr>
				<th>{_T string="Hours"}</th>
				<td>
					{$activity->horaires|escape|nl2br}
				</td>
			</tr>
			<tr>
				<th>{_T string="Informations"}</th>
				<td>
					{$activity->renseignements|escape|nl2br}
				</td>
			</tr>
			<tr>
				<th>{_T string="Full"}</th>
				<td>
					{if {$activity->complet|escape|nl2br} == 1}
						{_T string="Yes"}
					{else}
						{_T string="No"}
					{/if}
				</td>
			</tr>
			
		</table>
	</div>
{if $files_vierges> 0}	
<div class="bigtable wrmenu">
		<table class="details">
			<caption class="ui-state-active ui-corner-top">{_T string="List of needed files"}</caption>
			
			{foreach from=$files_vierges key=k item=file}
			<tr>
				<th>
					<a href="{$galette_base_path}{$subscription_dir}upload/files/{$file->emplacement|escape}" title="{$file->description|escape}" target="blank">{$file->doc_name|escape} </a>
				</th>
				<td>
					{$file->description|escape}
				</td>
			</tr>
			{/foreach}
		</table>
	</div>
{/if}				
	<div class="button-container">
		<a id="histreset" class="button" href="" onclick="javascript:window.close()">{_T string="Close"}</a>
	</div> 
	</BR>
	</BR>

			
	
