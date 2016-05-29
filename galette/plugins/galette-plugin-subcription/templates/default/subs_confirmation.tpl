 <p> 
{_T string="Form"} ->  {_T string="Send files"} -> <b><u>{_T string="Validation"}</u></b> -> {_T string="Payement"}
 </p>      

 </BR>
   <div id="successbox">{_T string="Subscription sent to staff for validation"}</div> 

 </BR>

  <div class="bigtable wrmenu">
		<table class="details">
			<caption class="ui-state-active ui-corner-top">{_T string="Subscription"} N&deg;{$subscription->id_abn}</caption>
			
			<tr>
				<th>{_T string="Date"}</th>
				<td>
					{$subscription->date_demande}
				</td>
			</tr>
			<tr>
				<th>{_T string="State"}</th>
				<td>
					{if $statut_abn == 0}
						<img src="{$subcription_tpl_dir}templates/default/images/orange.png" alt="{_T string="orange"}">
						{_T string="At least one activity is on progress or validated but not payed"}
					{/if}
					{if $statut_abn == 1}
						<img src="{$subcription_tpl_dir}templates/default/images/vert.png" alt="{_T string="green"}">
						{_T string="All activities are payed"}
					{/if}
					{if $statut_abn == 2}
						<img src="{$subcription_tpl_dir}templates/default/images/rouge.png" alt="{_T string="red"}">
						{_T string="At least one activity is refused"}
					{/if}
				</td>
			</tr>
			<tr>
				<th>{_T string="Estimated TOTAL:"}</th>
				<td>
					{$subscription->total_estimme} &euro;
				</td>
			</tr>
			
		</table>
	</div>
</BR>
 <div class="button-container">
	<FORM NAME="form" action="confirmation2.php" method="post" enctype="multipart/form-data"> 
		<input type="hidden" name="id_abn" value="{$subscription->id_abn}"/>
					
		<input type="submit" name="next" id="next" value="{_T string="Manage subcription"}"/>
	</FORM>
</div>