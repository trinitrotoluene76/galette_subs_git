{_T string="Activity selected"}
<select name="menu_destination" onchange="document.location = this.options[this.selectedIndex].value;">
	{foreach from=$activities key=id_act item=value}
		<option  value="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&nbligne=2" {if $id_act_s==$id_act}selected="selected"{/if}>{$activities[$id_act]->group_name}</option>
	{/foreach}
	<option  value="{$galette_base_path}{$subscription_dir}management_subs.php?id_act=all&nbligne=2" {if $id_act_s==""}selected="selected"{/if}>{_T string="All"}</option>
</select>
{if $login->isAdmin() == 1}
	<a id="histreset" class="button" href="{$galette_base_path}{$subscription_dir}confirmation_clean_adh.php" title="{_T string="Delete members that haven't subscribed to the parent activity during 2 years or that haven't modified their profile"}">{_T string="Clean adh DB"}</a>
{/if}
{foreach from=$activities key=id_act item=value}	

	{if $id_act_s==$id_act||$id_act_s==""}
		{if $valid==$id_act}
			<div id="successbox">{_T string="Subscriptions have been deleted"}</div>
		{/if}

		{if $select_nbligne==0}{$nbligne=0}{/if}
		{if $select_nbligne==1}{$nbligne=1}{/if}
		{if $select_nbligne==2}{$nbligne=2}{/if}
		{if $select_nbligne==3}{$nbligne=3}{/if}
		{if $select_nbligne==4}{$nbligne=4}{/if}

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
							<th>
								{if $order==1}
									<a href="{$galette_base_path}{$subscription_dir}management_subs.php?order=2{if $nbligne>0}&nbligne={$nbligne}&id_act={$id_act}{/if}"># <img src="{$template_subdir}/images/up.png"></a>
								{/if}
								{if $order==2}
									<a href="{$galette_base_path}{$subscription_dir}management_subs.php?order=1{if $nbligne>0}&nbligne={$nbligne}&id_act={$id_act}{/if}"># <img src="{$template_subdir}/images/down.png"></a>
								{/if}
								{if $order==0 || $order==3 || $order==4 ||$order==5 || $order==6 || $order==7 || $order==8}
									<a href="{$galette_base_path}{$subscription_dir}management_subs.php?order=2{if $nbligne>0}&nbligne={$nbligne}&id_act={$id_act}{/if}">#</a>
								{/if}
							</th>
							<th width=10%>
								{if $order==1}
									<a href="{$galette_base_path}{$subscription_dir}management_subs.php?order=2{if $nbligne>0}&nbligne={$nbligne}&id_act={$id_act}{/if}">{_T string="Date"} <img src="{$template_subdir}/images/up.png"></a>
								{/if}
								{if $order==2}
									<a href="{$galette_base_path}{$subscription_dir}management_subs.php?order=1{if $nbligne>0}&nbligne={$nbligne}&id_act={$id_act}{/if}">{_T string="Date"} <img src="{$template_subdir}/images/down.png"></a>
								{/if}
								{if $order==0 || $order==3 || $order==4 ||$order==5 || $order==6 || $order==7 || $order==8}
									<a href="{$galette_base_path}{$subscription_dir}management_subs.php?order=2{if $nbligne>0}&nbligne={$nbligne}&id_act={$id_act}{/if}">{_T string="Date"}</a>
								{/if}
							</th>
							
							<th width=20%>
								{if $order==3}
									<a href="{$galette_base_path}{$subscription_dir}management_subs.php?order=4{if $nbligne>0}&nbligne={$nbligne}&id_act={$id_act}{/if}">{_T string="Name"} <img src="{$template_subdir}/images/up.png"></a>
								{/if}
								{if $order==4}
									<a href="{$galette_base_path}{$subscription_dir}management_subs.php?order=3{if $nbligne>0}&nbligne={$nbligne}&id_act={$id_act}{/if}">{_T string="Name"} <img src="{$template_subdir}/images/down.png"></a>
								{/if}
								{if $order==0 || $order==1 || $order==2 ||$order==5 || $order==6 || $order==7 || $order==8}
									<a href="{$galette_base_path}{$subscription_dir}management_subs.php?order=4{if $nbligne>0}&nbligne={$nbligne}&id_act={$id_act}{/if}">{_T string="Name"}</a>
								{/if}
								
							</th>
							
							<th>
								{_T string="Sub. status"}
							</th>
							 <th width=10%>
								{if $order==7 || $order==0}
									<a href="{$galette_base_path}{$subscription_dir}management_subs.php?order=8{if $nbligne>0}&nbligne={$nbligne}&id_act={$id_act}{/if}">{_T string="Activity status"} <img src="{$template_subdir}/images/up.png"></a>
								{/if}
								{if $order==1 || $order==2 ||$order==5 || $order==6 || $order==3 || $order==4}
									<a href="{$galette_base_path}{$subscription_dir}management_subs.php?order=8{if $nbligne>0}&nbligne={$nbligne}&id_act={$id_act}{/if}">{_T string="Activity status"}</a>
								{/if}
								{if $order==8}
									<a href="{$galette_base_path}{$subscription_dir}management_subs.php?order=7{if $nbligne>0}&nbligne={$nbligne}&id_act={$id_act}{/if}">{_T string="Activity status"} <img src="{$template_subdir}/images/down.png"></a>
								{/if}
														
							</th>
							<th width=17%>
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
								{$members[$id_act][$subscription->id_abn]->sname}
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
				<div class="button-container">
				{if $login->isAdmin() == 1}
					<a id="histreset" class="button" href="{$galette_base_path}{$subscription_dir}confirmation_reset_saison.php?remove_id_act={$id_act}" title="{_T string="Delete subscriptions for this activity only, files of subscribers for this activity and remove subscribers from the group"}">{_T string="New Sport Season"}</a>
				{/if}		
					{if $nbpages[$id_act]>0 }
						Pages: 
						
						{if $currentpages[$id_act]-3>0 && $nbpages[$id_act] > 5}
							{$debut=$currentpages[$id_act]-2}
							<a href="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&currentpage={$currentpages[$id_act]-1}{if $nbligne>0}&nbligne={$nbligne}{/if}{if $order>0}&order={$order}{/if}">< </a>
							{if $nbpages[$id_act] >= $currentpages[$id_act]+2 }
								{$fin=$currentpages[$id_act]+2}
								{for $k3=$debut to $fin}
									{if $currentpages[$id_act] == $k3 && $currentpages[$id_act]!=0}
										<b>
											<a href="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&currentpage={$k3}{if $nbligne>0}&nbligne={$nbligne}{/if}{if $order>0}&order={$order}{/if}">{$k3} </a>
										</b>
									{else}
											<a href="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&currentpage={$k3}{if $nbligne>0}&nbligne={$nbligne}{/if}{if $order>0}&order={$order}{/if}">{$k3} </a>
									{/if}
								{/for}
							{else}
								{if $nbpages[$id_act]-$currentpages[$id_act]<3}
									{$debut=$nbpages[$id_act]-4}
								{/if}
								{for $k3=$debut to $nbpages[$id_act]}
									{if $currentpages[$id_act] == $k3 && $currentpages[$id_act]!=0}
										<b>
											<a href="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&currentpage={$k3}{if $nbligne>0}&nbligne={$nbligne}{/if}{if $order>0}&order={$order}{/if}">{$k3} </a>
										</b>
									{else}
											<a href="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&currentpage={$k3}{if $nbligne>0}&nbligne={$nbligne}{/if}{if $order>0}&order={$order}{/if}">{$k3} </a>
									{/if}
								{/for}
							{/if}
							{if $nbpages[$id_act]-$currentpages[$id_act]>2 }
								<a href="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&currentpage={$currentpages[$id_act]+1}{if $nbligne>0}&nbligne={$nbligne}{/if}{if $order>0}&order={$order}{/if}"> ></a>
							{/if}
						{else}
							{$debut=1}
							{if $nbpages[$id_act] >= 5 }
								{$fin=5}
								{for $k3=$debut to $fin}
									{if $currentpages[$id_act] == $k3 && $currentpages[$id_act]!=0}
										<b>
											<a href="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&currentpage={$k3}{if $nbligne>0}&nbligne={$nbligne}{/if}{if $order>0}&order={$order}{/if}">{$k3} </a>
										</b>
									{else}
											<a href="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&currentpage={$k3}{if $nbligne>0}&nbligne={$nbligne}{/if}{if $order>0}&order={$order}{/if}">{$k3} </a>
									{/if}
								{/for}
							{if $nbpages[$id_act]-$currentpages[$id_act]>2 }
								<a href="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&currentpage={$currentpages[$id_act]+1}{if $nbligne>0}&nbligne={$nbligne}{/if}{if $order>0}&order={$order}{/if}"> ></a>
							{/if}
							{else}
								{for $k3=$debut to $nbpages[$id_act]}
									{if $currentpages[$id_act] == $k3 && $currentpages[$id_act]!=0}
										<b>
											<a href="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&currentpage={$k3}{if $nbligne>0}&nbligne={$nbligne}{/if}{if $order>0}&order={$order}{/if}">{$k3} </a>
										</b>
									{else}
											<a href="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&currentpage={$k3}{if $nbligne>0}&nbligne={$nbligne}{/if}{if $order>0}&order={$order}{/if}">{$k3} </a>
									{/if}
								{/for}
							{/if}
						{/if}
					{/if}
					
					
						{_T string="Number of lignes: "}
						<select name="menu_destination" onchange="document.location = this.options[this.selectedIndex].value;">
									<option  value="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&nbligne=1{if $order>0}&order={$order}{/if}" {if $select_nbligne==1}selected="selected"{/if}>10</option>
									<option  value="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&nbligne=2{if $order>0}&order={$order}{/if}" {if $select_nbligne==2}selected="selected"{/if}>20</option>
									<option  value="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&nbligne=3{if $order>0}&order={$order}{/if}" {if $select_nbligne==3}selected="selected"{/if}>50</option>
									<option  value="{$galette_base_path}{$subscription_dir}management_subs.php?id_act={$id_act}&nbligne=4{if $order>0}&order={$order}{/if}" {if $select_nbligne==4}selected="selected"{/if}>{_T string="All"}</option>
						</select>
					<a id="btnsave" class="button" target="blanck" href="{$galette_base_path}{$subscription_dir}export_subs.php?id_act={$id_act}">Export</a>
				</div>
	{/if}
{/foreach}	

