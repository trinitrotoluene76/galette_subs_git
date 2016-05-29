{if $valid== 1 }
	<div id="successbox">{_T string="Subscriber added to group:"} {$activities[$followup->id_act]->group_name}</div>
{/if}
{if $followups[$activity2->id_group]->statut_act == 0 || $followups[$activity2->id_group]->statut_act == 3}
{else}
							
{/if}
<form action="management_subs2.php?id_act={$followup->id_act}&id_abn={$followup->id_abn}&id_adh={$followup->id_adh}" method="post" enctype="multipart/form-data" id="form">
	<div class="bigtable wrmenu">
		<table class="details">
			<caption class="ui-state-active ui-corner-top">
				{$activities[$followup->id_act]->group_name}</br>
				{if $followups[$activity2->id_group]->statut_act == 0 || $followups[$activity2->id_group]->statut_act == 3}
				{_T string="Follow up the request"} N&deg;{$subscription->id_abn} {_T string="of1"} {$subscription->date_demande} {_T string="of2"} <a style="color:blue" class="link" href="{$galette_base_path}/voir_adherent_read_only.php?id_adh={$member->id}" target="blank" title="{_T string="View subscriber to modify informations & photo"}">{$member->surname|escape|nl2br} {$member->name|escape|nl2br}</a>			
					{else}
					{_T string="Follow up the request"} N&deg;{$subscription->id_abn} {_T string="of1"} {$subscription->date_demande} {_T string="of2"} <a style="color:blue" class="link" href="{$galette_base_path}/voir_adherent.php?id_adh={$member->id}" target="blank" title="{_T string="View subscriber to modify informations & photo"}">{$member->surname|escape|nl2br} {$member->name|escape|nl2br}</a>			
				{/if}
				
			</caption>
			
			<tr>
				<th>{_T string="Photo"}</th>
				<td COLSPAN=2>
					{if $picture == 0}
						{_T string="No"} ->
						{if $followups[$activity2->id_group]->statut_act == 0 || $followups[$activity2->id_group]->statut_act == 3}
						<a href="{$galette_base_path}voir_adherent_read_only.php?id_adh={$member->id}" title="{_T string="Add picture on subscriber profile"}" target="blank">{_T string="Add picture on subscriber profile"}</a>	
							{else}
							<a href="{$galette_base_path}voir_adherent.php?id_adh={$member->id}" title="{_T string="Add picture on subscriber profile"}" target="blank">{_T string="Add picture on subscriber profile"}</a>							
							{/if}
						
					{/if}
					{if $picture == 1}
						{_T string="Yes"}
					{/if}
				</td>
			</tr>
			
			<tr>
				<th>{_T string="Subscriber status"}</th>
				<td COLSPAN=2>
					{$statut}
				</td>
			</tr>
			<tr>
				<th>{_T string="Age"}</th>
				<td COLSPAN=2>
					{$age} {_T string="ans"}
				</td>
			</tr>
			
			<tr>
				<th>{_T string="Activity subscriber message"}</th>
				<td COLSPAN=2>
					{$followup->message_adh_act|escape|nl2br}
				</td>
			</tr>
			<tr>
				<th>{_T string="Subscribtion subscriber message"}</th>
				<td COLSPAN=2>
					{$subscription->message_abn|escape|nl2br}
				</td>
			</tr>
			<tr>
				<th>{_T string="Activity feedback"}</th>
				<td COLSPAN=2>
					<TEXTAREA NAME="feedback_act" ROWS=2 COLS=20>{$followup->feedback_act|escape}</TEXTAREA>
				</td>
			</tr>
			<tr>
				<th>{_T string="Activity feedback off"}</th>
				<td COLSPAN=2>
					<TEXTAREA NAME="feedback_act_off" ROWS=2 COLS=20>{$followup->feedback_act_off|escape}</TEXTAREA>
				</td>
			</tr>
			<tr>
				<th>{_T string="Activity status"}</th>
				<td COLSPAN=2>
					<SELECT NAME="statut_act"> 
						<OPTION VALUE="0" {if $followup->statut_act == 0}selected="selected"{/if}>{_T string="In progress"}
						<OPTION VALUE="1" {if $followup->statut_act == 1}selected="selected"{/if}>{_T string="Validated"} 
						<OPTION VALUE="2" {if $followup->statut_act == 2}selected="selected"{/if}>{_T string="Paid"} 
						<OPTION VALUE="3" {if $followup->statut_act == 3}selected="selected"{/if}>{_T string="Refused"}
					</SELECT> 
					{_T string="Actual value:"} 
					{if $followup->statut_act == 0}
						{_T string="In progress"}
					{/if}
					{if $followup->statut_act == 1}
						{_T string="Validated"}
					{/if}
					{if $followup->statut_act == 2}
						{_T string="Paid"}
					{/if}
					{if $followup->statut_act == 3}
						{_T string="Refused"}
					{/if}
						
				</td>
			</tr>
			<tr>
				<th>{_T string="Price category"}</th>
				<td COLSPAN=2>
					{if $category == 0}
						(tarif1) Personnel Nexter et assimil&eacute;s
					{/if}
					{if $category == 1}
						(tarif2) Enfant du Personnel Nexter et assimil&eacute;s &lsaquo;18 ans
					{/if}
					{if $category == 2}
						(tarif3) Enfant du Personnel Nexter et assimil&eacute;s &lsaquo;=25 ans
					{/if}
					{if $category == 3}
						(tarif4) Ext&eacute;rieurs et Enfants du Personnel Nexter &rsaquo;25 ans
					{/if}
					
				</td>
			</tr>
			<tr>
				<th>{_T string="Estimated Total"}</th>
				<td COLSPAN=2>
					{$subscription->total_estimme} &euro;
				</td>
			</tr>
			<tr>
				<th>{_T string="Activities linked"}</th>
				<td>
					{foreach from=$activities key=k item=activity2}
						<a title="{_T string="feedback"}: {$followups[$activity2->id_group]->feedback_act|escape}, &#13;{_T string="feedback off"}: {$followups[$activity2->id_group]->feedback_act_off|escape}">
							-{$activity2->group_name} / 
							{if $followups[$activity2->id_group]->statut_act == 0}
								{_T string="In progress"}
							{/if}
							{if $followups[$activity2->id_group]->statut_act == 1}
								{_T string="Validated"}
							{/if}
							{if $followups[$activity2->id_group]->statut_act == 2}
								{_T string="Paid"}
							{/if}
							{if $followups[$activity2->id_group]->statut_act == 3}
								{_T string="Refused"}
							{/if}
							/
							{if $category == 0}
								{$activity2->price1|escape|nl2br} &euro;
							{/if}
							{if $category == 1}
								{$activity2->price2|escape|nl2br} &euro;
							{/if}
							{if $category == 2}
								{$activity2->price3|escape|nl2br} &euro;
							{/if}
							{if $category == 3}
								{$activity2->price4|escape|nl2br} &euro;
							{/if}
						</a>
					</br>
					{/foreach}
				</td>
				<td>
					{if $statut_abn == 0}
						<img src="{$subcription_tpl_dir}templates/default/images/orange.png" alt="{_T string="orange"}" title="{_T string="At least one activity is on progress or validated but not payed"}">
					{/if}
					{if $statut_abn == 1}
						<img src="{$subcription_tpl_dir}templates/default/images/vert.png" alt="{_T string="green"}"  title="{_T string="All activities are payed"}">
					{/if}
					{if $statut_abn == 2}
						<img src="{$subcription_tpl_dir}templates/default/images/rouge.png" alt="{_T string="red"}" title="{_T string="At least one activity is refused"}">
						
					{/if}
				</td>
				
			</tr>
			
		</table>
	</div>
				
			<div class="button-container">
				<a id="prev" class="button" href="{$galette_base_path}{$subscription_dir}management_subs.php">{_T string="Previous"}</a>
				<a id="btnadd" class="button" href="{$galette_base_path}{$subscription_dir}send_files_standalone.php?id_act={$followup->id_act}&id_abn={$followup->id_abn}&id_adh={$followup->id_adh}&vierge=0" target="blank">{_T string="Add/view files"}</a>
				<a id="next" class="button" href="{$galette_base_path}gestion_transactions.php" target="blank">{_T string="View transactions"}</a>
				<a id="next" class="button" href="{$galette_base_path}gestion_contributions.php" target="blank">{_T string="View contributions"}</a>
				<input type="submit" name="valid" id="btnsave" value="{_T string="Save"}"/>
			</div>
	</form> 

	
