 <p> 
{_T string="Form"} ->  {_T string="Send files"} -> <b><u>{_T string="Validation"}</u></b> -> {_T string="Payement"}
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
                        {_T string="Estimated total"}
                    </th>
                    
                    <th class="left">
                        {_T string="Activities"}
                    </th>
					<th class="left">
                        {_T string="Details"}
                    </th>
                </tr>
            </thead>
			
			{foreach from=$subscriptions key=k item=subscription}	
										
				<tr>
					<td>
						{$subscription->id_abn}						
					</td>
					<td width=90px>
						{$subscription->date_demande}
					</td>
					<td>
						{$subscription->total_estimme} &euro;
					</td>
					<td>
					{foreach from=$activities[$subscription->id_abn] key=k2 item=activity}
						-{$activity->group_name|escape}</br>
					{/foreach}
					</td>
					<td>
						{if $statut_abns[{$subscription->id_abn}] == 0}
							<img src="{$subcription_tpl_dir}templates/default/images/orange.png" alt="{_T string="orange"}">
							{_T string="At least one activity is on progress or validated but not payed"}
						{/if}
						{if $statut_abns[{$subscription->id_abn}] == 1}
							<img src="{$subcription_tpl_dir}templates/default/images/vert.png" alt="{_T string="green"}">
							{_T string="All activities are payed"}
						{/if}
						{if $statut_abns[{$subscription->id_abn}] == 2}
							<img src="{$subcription_tpl_dir}templates/default/images/rouge.png" alt="{_T string="red"}">
							{_T string="At least one activity is refused"}
						{/if}
						
						
						</br>
						<a href="{$galette_base_path}{$subscription_dir}confirmation2.php?id_abn={$subscription->id_abn}" title="{_T string="View follow up"}">{_T string="View follow up"}</a>
					</td>	
				</tr>
			{/foreach}	
				
        </table>
</div>
