{foreach from=$activities key=id_act item=value}	
{if $valid==$id_act}
	<div id="successbox">{_T string="Subscriptions have been deleted"}</div>
{/if}

<p> 
	<b>{_T string="Activity"}: {$activities[$id_act]->group_name}
	/  {_T string="Full"}: 
	{if $activities[$id_act]->complet == 0}
	{_T string="No"}
	{/if}
	{if $activities[$id_act]->complet == 1}
	{_T string="Yes"}
	{/if}
	/ {_T string="Number of subscribers:"} {$nbinscr[$id_act]}</b>
 </p> 
 
	<div class="bigtable">
        <table class="listing adherents" >
            <thead>
               <tr>
                    <th class="left date_row">
						{_T string="Subscription"} N&deg;
                    </th>
                    <th class="left date_row">
						{_T string="Date"}    
                    </th>
                    
					<th class="left date_row">
                        {_T string="Name"}
                    </th>
                    
                    <th class="left">
                        {_T string="Sub. status"}
                    </th>
					 <th class="left">
                        {_T string="Activity status"}
                    </th>
					<th class="left">
                        {_T string="Details"}
                    </th>
                </tr>
            </thead>
			
			{foreach from=$subscriptions[$id_act] key=k2 item=subscription}	
										
				<tr>
					<td>
						{$subscription->id_abn}						
					</td>
					<td width=90px>
						{$subscription->date_demande}
					</td>
					<td>
						{$members[$id_act][$subscription->id_abn]->surname} {$members[$id_act][$subscription->id_abn]->name}
					</td>
					<td>
						{$statuts[$id_act][$subscription->id_abn]}
					</td>
					<td>
						{if $followups[$id_act][$subscription->id_abn]->statut_act == 0}
							{_T string="In progress"}
						{/if}
						{if $followups[$id_act][$subscription->id_abn]->statut_act == 1}
							{_T string="Validated"}
						{/if}
						{if $followups[$id_act][$subscription->id_abn]->statut_act == 2}
							{_T string="Paid"}
						{/if}
						{if $followups[$id_act][$subscription->id_abn]->statut_act == 3}
							{_T string="Refused"}
						{/if}
					</td>
					<td>
						<a href="{$galette_base_path}{$subscription_dir}management_subs2.php?id_act={$id_act}&id_abn={$subscription->id_abn}&id_adh={$members[$id_act][$subscription->id_abn]->id}" title="{_T string="View follow up details and modify fields"}">{_T string="View follow up & modify"}</a>
					</td>
										
				</tr>
			{/foreach}	
				
        </table>
	</div>
	</br>
	{if $login->isStaff() == 1}
		<div class="button-container">
			<a id="histreset" class="button" href="{$galette_base_path}{$subscription_dir}management_subs.php?remove_id_act={$id_act}" title="{_T string="Delete subscriptions for this activity only, files of subscribers for this activity and remove subscribers from the group"}">{_T string="New Sport Season"}</a>
		</div>
	{/if}
{/foreach}	
