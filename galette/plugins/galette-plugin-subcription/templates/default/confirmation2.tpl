 <p> 
{_T string="Form"} ->  {_T string="Send files"} -> {_T string="Validation"} -> <b><u>{_T string="Payement"}</u></b>
 </p> 
<p> 
<b><u>{_T string="Follow up of subscription"} N&deg;{$subscription->id_abn} {_T string="recorded on :"} {$subscription->date_demande}</u></b>
 </p>  
 	<div class="bigtable">
        <table class="listing adherents" >
            <thead>
               <tr>
                    <th class="left date_row">
						{_T string="Activity"}
                    </th>
                    <th class="left date_row">
						{_T string="Status"}
                    </th>
                    
					<th class="left date_row">
                        {_T string="Your comment"}
                    </th>
                    <th class="left date_row">
                        {_T string="Answer of staff"}
                    </th>
                    
                    <th class="left">
                        {_T string="Amount due"}
                    </th>
                </tr>
            </thead>
			
				
	{foreach from=$activities key=k item=activity}
										
				<tr>
					<td width="20%">
						<b>{$activity->group_name|escape|nl2br}</b>
						</br>
						<div>
							<a class="button" id="btnadd" href="{$galette_base_path}{$subscription_dir}send_files_standalone.php?id_act={$activity->id_group}&vierge=0&id_abn={$subscription->id_abn}" title="{_T string="Add/view file"}" target="blank">{_T string="Add/view file"}</a>
						</div>
					</td>
					<td>
						{if $followups[$k]->statut_act == 0}
							{_T string="In progress"}
						{/if}
						{if $followups[$k]->statut_act == 1}
							{_T string="Validated"}
						{/if}
						{if $followups[$k]->statut_act == 2}
							{_T string="Paid"}
						{/if}
						{if $followups[$k]->statut_act == 3}
							{_T string="Refused"}
						{/if}
						
					</td>
					<td width="20%">
						<div>
							{$followups[$k]->message_adh_act|escape|nl2br}
						</div>
					</td>
					<td width="20%">
						<div>
							<span style="color:green">
								{if $followups[$k]->statut_act == 1}
									{_T string="You can pay for this activity"}</br>
								{/if}
								{$followups[$k]->feedback_act|escape|nl2br}
							</span>
						</div>
						
					</td>
					<td>
						{if $followups[$k]->statut_act == 1}
							{if $category == 0}
								<INPUT TYPE="hidden" NAME="price" VALUE="{$activity->price1|escape|nl2br}" readonly="readonly">
								{$activity->price1|escape|nl2br} &euro;
							{/if}
							{if $category == 1}
								<INPUT TYPE="hidden" NAME="price" VALUE="{$activity->price2|escape|nl2br}" readonly="readonly">
								{$activity->price2|escape|nl2br} &euro;
							{/if}
							{if $category == 2}
								<INPUT TYPE="hidden" NAME="price" VALUE="{$activity->price3|escape|nl2br}" readonly="readonly">
								{$activity->price3|escape|nl2br} &euro;
							{/if}
							{if $category == 3}
								<INPUT TYPE="hidden" NAME="price" VALUE="{$activity->price4|escape|nl2br}" readonly="readonly">
								{$activity->price4|escape|nl2br} &euro;
							{/if}
						{/if}
						{if $followups[$k]->statut_act == 0}
							{_T string="In progress"}
						{/if}
						{if $followups[$k]->statut_act == 2}
							{_T string="Paid"}
						{/if}
						{if $followups[$k]->statut_act == 3}
							{_T string="Refused"}
						{/if}
					</td>
				</tr>
	{/foreach}			
				<tr>
					
					<td colspan="2">
					</td>
					<td>
						<div  align="left">
							<i>{_T string="Subscription message:"}</br>
							{$subscription->message_abn|escape|nl2br}</i>
						</div>
						
					</td>
					<td>
					</td>
					<td>
					</td>
				</tr>
				<tr>	
					<th colspan="4">
						<div align="right">{_T string="TOTAL Amount:"}</div>
					</th>
					<th>
						{$total} &euro;
					</th>
					
				</tr>
        </table>
</div>
</BR>
<div class="button-container">
	<a class="button" id="next" href="{$galette_base_path}{$subscription_dir}follow_up_subs.php">{_T string="View all subscriptions"}</a>
</div>