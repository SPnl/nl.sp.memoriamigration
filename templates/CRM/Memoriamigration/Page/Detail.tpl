<div class="action-link">
    <a href="{crmURL p="civicrm/admin/memoria"}" class="button"><span>
            <div class="icon back-icon"></div> Overzicht</span></a>
    {if in_array($group->status, array('none','notmigrated','error'))}
        <a href="{crmURL p="civicrm/admin/memoria/do" q="action=migration_confirm&id=`$group->id`"}" class="button">
            <span><div class="icon swap-icon"></div> Migreren</span></a>
    {/if}
    {if $group->status == 'migrated'}
        <a href="{crmURL p="civicrm/admin/memoria/do" q="action=readonly&id=`$group->id`"}" class="button">
            <span>Alleen-lezen maken</span></a>
    {/if}
</div>

<table style="width:100%">
    <tr>
        <td><strong>Groep:</strong></td>
        <td><strong>{$group->usergroup_name}</strong></td>
    </tr>
    <tr>
        <td>Memoria-ID:</td>
        <td>{$group->usergroup_id}</td>
    </tr>
    <tr>
        <td>Memoria-rechtenfilter:</td>
        <td>{$group->usergroup_filter}</td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td><strong>Status:</strong></td>
        <td><strong>{if $group->status == 'none'}
                    Nog niet gepland
                {elseif $group->status == 'notmigrated'}
                    Nog niet gemigreerd
                {elseif $group->status == 'queued'}
                    In de wachtrij
                {elseif $group->status == 'migrating'}
                    Bezig met migreren
                {elseif $group->status == 'migrated'}
                    Gemigreerd
                {elseif $group->status == 'readonly'}
                    Gemigreerd, alleen-lezen
                {elseif $group->status == 'error'}
                    Error
                {/if}</strong></td>
    </tr>
    <tr>
        <td>Gemigreerd op:</td>
        <td>{if $group->status == 'migrated' or $group->status == 'readonly'}
                {$group->migrated|date_format:'%d-%m-%Y'}
            {else}-{/if}</td>
    </tr>
    <tr>
        <td>Opmerkingen</td>
        <td>{$group->notes|default:'-'}</td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td>Gebruikers ({$detail.users|@count}):</td>
        <td>
            {foreach from=$detail.users item=user}- {$user.user} ({$user.name} / {$user.email})<br/>{/foreach}
        </td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td>Aantal leden:</td>
        <td>{$detail.memberCount}</td>
    </tr>
    <tr>
        <td>Aantal leden met opmerkingen:</td>
        <td>{$detail.commentCount}</td>
    </tr>
    <tr>
        <td>Aantal lokale eigenschappen:</td>
        <td>{$detail.propertyCount}</td>
    </tr>
    <tr>
        <td>Aantal handmatige selecties:</td>
        <td>{$detail.manselCount}</td>
    </tr>
    <tr>
        <td>Totaal aantal mutaties:</td>
        <td>{$detail.changeCount}</td>
    </tr>
</table>