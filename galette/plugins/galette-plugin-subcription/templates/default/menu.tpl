{if $login->isGroupManager() == 1 && $login->isSuperAdmin() != 1}
	<h1 class="nojs">{_T string="Subscription"}</h1>
	<ul>
		<li {if $PAGENAME eq "new_subscription.php" || $PAGENAME eq "send_files.php" || $PAGENAME eq "view_activity.php"} class="selected"{/if}><a href="{$galette_base_path}{$galette_galette_subscription_path}new_subscription.php" title="{_T string="New resquest of subscription"}">{_T string="New request"}</a></li>
		<li {if $PAGENAME eq "follow_up_subs.php" || $PAGENAME eq "confirmation2.php" || $PAGENAME eq "subs_confirmation.php"} class="selected"{/if}><a href="{$galette_base_path}{$galette_galette_subscription_path}follow_up_subs.php" title="{_T string="Follow up of requests"}">{_T string="Follow up"}</a></li>
		<li {if $PAGENAME eq "management_groups.php"} class="selected"{/if}><a href="{$galette_base_path}{$galette_galette_subscription_path}management_groups.php" title="{_T string="Management of groups"}">{_T string="Management grps"}</a></li>
		<li {if $PAGENAME eq "management_subs.php" || $PAGENAME eq "management_subs2.php"} class="selected"{/if}><a href="{$galette_base_path}{$galette_galette_subscription_path}management_subs.php" title="{_T string="Management of subscribers"}">{_T string="Management subs"}</a></li>
	</ul>
{/if}
{if $login->isSuperAdmin()}
	<h1 class="nojs">{_T string="Subscription"}</h1>
	<ul>
		<li {if $PAGENAME eq "management_groups.php"} class="selected"{/if}><a href="{$galette_base_path}{$galette_galette_subscription_path}management_groups.php" title="{_T string="Management of groups"}">{_T string="Management grps"}</a></li>
		<li {if $PAGENAME eq "management_subs.php" || $PAGENAME eq "management_subs2.php"} class="selected"{/if}><a href="{$galette_base_path}{$galette_galette_subscription_path}management_subs.php" title="{_T string="Management of subscribers"}">{_T string="Management subs"}</a></li>
	</ul>
{/if}
{if $login->isGroupManager() eq 0}
	<h1 class="nojs">{_T string="Subscription"}</h1>
	<ul>
		<li {if $PAGENAME eq "new_subscription.php" || $PAGENAME eq "view_activity.php"} class="selected"{/if}><a href="{$galette_base_path}{$galette_galette_subscription_path}new_subscription.php" title="{_T string="New resquest of subscription"}">{_T string="New request"}</a></li>
		<li {if $PAGENAME eq "follow_up_subs.php" || $PAGENAME eq "confirmation2.php"} class="selected"{/if}><a href="{$galette_base_path}{$galette_galette_subscription_path}follow_up_subs.php" title="{_T string="Follow up of requests"}">{_T string="Follow up"}</a></li>
	</ul>
{/if}