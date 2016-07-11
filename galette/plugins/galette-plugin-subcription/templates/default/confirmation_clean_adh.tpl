{if $countmb>0}
{_T string="Do you really want to delete this members? (They haven't subscribed to the parent activity nor modified their profile since more than 2 years)"}
	<div class="button-container">
		<a id="histreset" class="button" href="{$galette_base_path}{$subscription_dir}management_subs.php">{_T string="Previous"}</a>
		<a class="button" id="next" href="{$galette_base_path}{$subscription_dir}management_subs.php?clean_adh=1">{_T string="Yes"}</a>
	</div> 
	
		<p> 
			<b>{_T string="Members list to delete"}: ({$countmb})</b>
		 </p> 
		 
			<div class="bigtable">
				<table class="listing adherents" >
					<thead>
					   <tr>
							<th width=7%>
								# 
							</th>
							<th>
								{_T string="Name"}
							</th>
							
							<th width=20%>
								{_T string="Modification date or last subscription"}
							</th>
						</tr>
					</thead>
					
					{foreach from=$members key=k item=value}	
												
						<tr>
							<td>
								{$k}						
							</td>
							<td>
								{$members[$k]->sname}
							</td>
							<td>
								{$members[$k]->modification_date}
							</td>												
						</tr>
					{/foreach}	
						
				</table>
			</div>
{else}
{_T string="There is no member to delete. ( No member they haven't subscribed to the parent activity nor modified their profile since more than 2 years)"}
<div class="button-container">
	<a id="histreset" class="button" href="{$galette_base_path}{$subscription_dir}management_subs.php">{_T string="Previous"}</a>
</div> 
{/if}		 
	</BR>
	</BR>

			
	
