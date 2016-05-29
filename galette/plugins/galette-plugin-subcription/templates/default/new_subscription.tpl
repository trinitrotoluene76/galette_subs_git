 <p> 
 <b><u>{_T string="Form"}</u></b> -> {_T string="Send files"} -> {_T string="Validation"} -> {_T string="Payement"}
 </p>      
<div class="bigtable wrmenu" >
        <table class="listing adherents" >
            <thead>
               <tr>
                    <th class="left date_row">
						{_T string="tick box"}
                    </th>
                    <th class="left date_row">
						{_T string="List of activities"}
                    </th>
                    
					<th class="left date_row">
                        {_T string="Price* (&euro;)"}
                    </th>
                    
                    <th class="left">
                        {_T string="Details"}
                    </th>
                </tr>
            </thead>
			
				
<FORM NAME="form1" action="send_files.php" method="post" enctype="multipart/form-data" > 
	{foreach from=$activities key=k item=activity}
										
				<tr>
					<td>
						{if $activity->is_parent_group() == 1 && $members[$k] == 0}
							{if $category == 0}
								<INPUT TYPE="CHECKBOX" NAME="{$activity->id_group}" VALUE="{$activity->price1|escape}" checked="checked" disabled="disabled"><BR>
							{/if}
							{if $category == 1}
								<INPUT TYPE="CHECKBOX" NAME="{$activity->id_group}" VALUE="{$activity->price2|escape}" checked="checked" disabled="disabled"><BR>
							{/if}
							{if $category == 2}
								<INPUT TYPE="CHECKBOX" NAME="{$activity->id_group}" VALUE="{$activity->price3|escape}" checked="checked" disabled="disabled"><BR>
							{/if}
							{if $category == 3}
								<INPUT TYPE="CHECKBOX" NAME="{$activity->id_group}" VALUE="{$activity->price4|escape}" checked="checked" disabled="disabled"><BR>
							{/if}
							<input type="hidden" name="id_parent_group_checked" value="{$activity->id_group}"/>
						
						{elseif $members[$k] == 1 || $activity->is_full($activity) ==1}
							{if $category == 0}
								<INPUT TYPE="CHECKBOX" NAME="{$activity->id_group}" VALUE="{$activity->price1|escape}" disabled="disabled"><BR>
							{/if}
							{if $category == 1}
								<INPUT TYPE="CHECKBOX" NAME="{$activity->id_group}" VALUE="{$activity->price2|escape}" disabled="disabled"><BR>
							{/if}
							{if $category == 2}
								<INPUT TYPE="CHECKBOX" NAME="{$activity->id_group}" VALUE="{$activity->price3|escape}" disabled="disabled"><BR>
							{/if}
							{if $category == 3}
								<INPUT TYPE="CHECKBOX" NAME="{$activity->id_group}" VALUE="{$activity->price4|escape}" disabled="disabled"><BR>
							{/if}
							
						{else}
							{if $category == 0}
								<INPUT TYPE="CHECKBOX" NAME="{$activity->id_group}" VALUE="{$activity->price1|escape}" onClick="reponse(this.form)"><BR>
							{/if}
							{if $category == 1}
								<INPUT TYPE="CHECKBOX" NAME="{$activity->id_group}" VALUE="{$activity->price2|escape}" onClick="reponse(this.form)"><BR>
							{/if}
							{if $category == 2}
								<INPUT TYPE="CHECKBOX" NAME="{$activity->id_group}" VALUE="{$activity->price3|escape}" onClick="reponse(this.form)"><BR>
							{/if}
							{if $category == 3}
								<INPUT TYPE="CHECKBOX" NAME="{$activity->id_group}" VALUE="{$activity->price4|escape}" onClick="reponse(this.form)"><BR>
							{/if}
							
						{/if}
						 
					</td>
					<td>
						{$activity->group_name|escape}
					</td>
					<td>
						{if $category == 0}
							{$activity->price1|escape} &euro;
						{/if}
						{if $category == 1}
							{$activity->price2|escape} &euro;
						{/if}
						{if $category == 2}
							{$activity->price3|escape} &euro;
						{/if}
						{if $category == 3}
							{$activity->price4|escape} &euro;
						{/if}
						
					</td>
					<td>
						<a href="{$galette_base_path}{$subscription_dir}view_activity.php?id_group={$activity->id_group}" title="{_T string="view details of activity"}" target="blank">{_T string="view details"}</a>
					</td>
				</tr>
	{/foreach}			
				<tr>
					<td>
					</td>
					<th>{_T string="Estimated TOTAL"}
					</th>
					<td>
						<INPUT TYPE="TEXT" NAME="total" VALUE="0" readonly="readonly"><BR> 
						
					</td>
					<td>
					</td>
				</tr>
        </table>
</div>
</BR>
<div class="button-container">
	<input type="submit" name="next" id="next" value="{_T string="Next"}" onmouseover="reponse(this.form)"/>
</FORM>
</div>
<p>
{_T string="*Prices are function of age and status. View details of activities for more informations"}</BR>
{_T string="You are"} {$age} {_T string="year old and your status is:"} {$statut}

</p>