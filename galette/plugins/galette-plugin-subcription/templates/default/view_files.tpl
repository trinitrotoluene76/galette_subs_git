{if $deleteok eq 1}
   <div id="successbox">{_T string="File deleted"}</div> 
{/if}
{if $deleteok eq 0}
   <div id="errorbox">{_T string="Your file hasn't been found"}</div> 
{/if}

{if $files> 0}
	<div class="bigtable">
        <table class="listing adherents" >
            <thead>
               <tr>
                    <th class="left date_row">
						{_T string="id"}
                    </th>
                    <th class="left date_row">
						{_T string="Doc name"}    
                    </th>
                    
					<th class="left date_row">
                        {_T string="Activity"}
                    </th>
                    
                    <th class="left">
                        {_T string="Subs"}
                    </th>
					<th class="left">
                        {_T string="Date"}
                    </th>
					<th class="left">
                        {_T string="Delete"}
                    </th>
                </tr>
            </thead>
			
			{foreach from=$files key=k item=file}	
										
				<tr>
					<td>
						{$file->id_doc}						
					</td>
					<td >
						<a href="{$galette_base_path}{$subscription_dir}upload/files/{$file->emplacement}" title="{$file->description|escape}">{$file->doc_name}</a>
					</td>
					<td>
						{$activities[$k]->group_name}
					</td>
					<td>
						{$file->id_abn}	
					</td>
					<td>
						{$file->date_record}
					</td>	
					<td>
						<a id="histreset" class="button" href="{$galette_base_path}{$subscription_dir}view_files.php?delete=1&id_doc={$file->id_doc}&id_adh={$file->id_adh}">{_T string="Delete"}</a>
					</td>
				</tr>
			{/foreach}	
				
        </table>
	</div>
{else}
	<div id="successbox">{_T string="You doesn't have any files"}</div> 
{/if}
</br>
<div class="button-container">
		<a id="histreset" class="button" href="" onclick="javascript:window.close()">{_T string="Close"}</a>
</div> 
	</BR>
	</BR>