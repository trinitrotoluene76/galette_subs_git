{if $error eq 0}
   <div id="successbox">{_T string="Group updated"}</div> 
{/if}
{if $error eq 1}
   <div id="errorbox">{_T string="Data aren't valid"}</div> 
{/if}

{foreach from=$activities key=k item=activity}
  
	<form action="management_groups.php" method="post" enctype="multipart/form-data" id="form{$k}">
			
	<div class="bigtable wrmenu">
		<table class="details">
			<caption class="ui-state-active ui-corner-top">{$activity->group_name|escape}{_T string=", Number of members"}: {$nbinscr[$k]}</caption>
			
			
			<tr>
				<th>{_T string="Manager"}</th>
				<td>
					{if isset($managers[$k][0])}
						{foreach from=$managers[$k] key=k2 item=bi}
							
							{$managers[$k][$k2]->surname|escape}, {$managers[$k][$k2]->name|escape}, {$managers[$k][$k2]->phone|escape}, <a href=mailto:{$managers[$k][$k2]->email|escape}>mail</a>
							</BR>
						{/foreach}
					
					{else}
						<a class="link" href="{$galette_base_path}gestion_groupes.php">{_T string="Nobody is manager, go to group management page"}</a>
					{/if}
				
				</td>
			</tr>
			
			{if $login->isStaff() == 1}
			<tr>
				<th>{_T string="Price 1"} (&euro;)</th>
				<td>
					<INPUT TYPE="TEXT" NAME="price1" VALUE="{$activity->price1|escape}"  SIZE=3 MAXLENGTH=20>
				</td>
			</tr>
			<tr>
				<th>{_T string="Price 2"} (&euro;)</th>
				<td>
					<INPUT TYPE="TEXT" NAME="price2" VALUE="{$activity->price2|escape}"  SIZE=3 MAXLENGTH=20>
				</td>
			</tr>
			<tr>
				<th>{_T string="Price 3"} (&euro;)</th>
				<td>
					<INPUT TYPE="TEXT" NAME="price3" VALUE="{$activity->price3|escape}"  SIZE=3 MAXLENGTH=20>
				</td>
			</tr>
			<tr>
				<th>{_T string="Price 4"} (&euro;)</th>
				<td>
					<INPUT TYPE="TEXT" NAME="price4" VALUE="{$activity->price4|escape}"  SIZE=3 MAXLENGTH=20>
				</td>
			</tr>
			{else}
			<tr>
				<th>{_T string="Price 1"} (&euro;)</th>
				<td>
					{$activity->price1|escape}
				</td>
			</tr>
			<tr>
				<th>{_T string="Price 2"} (&euro;)</th>
				<td>
					{$activity->price2|escape}
				</td>
			</tr>
			<tr>
				<th>{_T string="Price 3"} (&euro;)</th>
				<td>
					{$activity->price3|escape}
				</td>
			</tr>
			<tr>
				<th>{_T string="Price 4"} (&euro;)</th>
				<td>
					{$activity->price4|escape}
				</td>
			</tr>
			{/if}
			<tr>
				<th>{_T string="Place"}</th>
				<td>
					<TEXTAREA NAME="lieu" ROWS=2 COLS=50>{$activity->lieu|escape}</TEXTAREA>
				</td>
			</tr>
			<tr>
				<th>{_T string="Day"}</th>
				<td>
					<TEXTAREA NAME="jours" ROWS=2 COLS=50>{$activity->jours|escape}</TEXTAREA>
				</td>
			</tr>
			<tr>
				<th>{_T string="Hours"}</th>
				<td>
					<TEXTAREA NAME="horaires" ROWS=2 COLS=50>{$activity->horaires|escape}</TEXTAREA>
				</td>
			</tr>
			<tr>
				<th>{_T string="Informations"}</th>
				<td>
					<TEXTAREA NAME="renseignements" ROWS=2 COLS=50>{$activity->renseignements|escape}</TEXTAREA>
				</td>
			</tr>
			<tr>
				<th>{_T string="Full"}</th>
				<td>
					<select name="complet">
						<option name="0"  value="0">{_T string="no"}
						<option name="1" value="1">{_T string="yes"}
					</select>
					{_T string="Actual value"}: 
					{if $activity->complet == 0}
						{_T string="No"}
					{/if}
					{if $activity->complet == 1}
						<b>{_T string="Yes"}</b>
					{/if}
				</td>
			</tr>
		{if $login->isStaff() == 1}
			<tr>
				<th>{_T string="Auto validation"}</th>
				<td>
					<select name="autovalidation">
						<option name="0"  value="0">{_T string="no"}
						<option name="1" value="1">{_T string="yes"}
					</select>
					{_T string="Actual value"}: 
					{if $activity->autovalidation == 0}
						{_T string="No"}
					{/if}
					{if $activity->autovalidation == 1}
						<b>{_T string="Yes"}</b>
					{/if}
				</td>
			</tr>
		{/if}	
		</table>
	</div>
				
			<div class="button-container">
				<a id="btnadd" class="button" href="{$galette_base_path}{$subscription_dir}send_files_standalone.php?id_act={$activity->id_group}&vierge=1" target="blank">{_T string="Add files or forms for the group"}</a>
				<input type="submit" name="valid" id="btnsave" value="{_T string="Save"}"/>
				<input type="hidden" name="id_group" value="{$activity->id_group}"/>
				<input type="hidden" name="id_form" value="{$k}"/>
			</div>
	</form> 
	</BR>
	</BR>
{/foreach}
			
	
