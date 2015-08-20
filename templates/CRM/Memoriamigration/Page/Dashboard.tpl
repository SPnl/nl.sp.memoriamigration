<div class="action-link">
    <a href="{crmURL p='civicrm/admin/memoria/settings'}" class="button"><span><div class="icon settings-icon"></div> Instellingen</span></a>
    <a href="{crmURL p='civicrm/admin/memoria/refresh'}" class="button"><span><div class="icon refresh-icon"></div> Groepen verversen</span></a>
</div>

<table style="width:100%">
    <tr>
        <th><strong>Memoria-ID</strong></th>
        <th><strong>Groep</strong></th>
        <th><strong>Status</strong></th>
        <th><strong>Acties</strong></th>
    </tr>
    {foreach from=$groups item=group}
        <tr>
            <td>{$group.usergroup_id}</td>
            <td>{$group.usergroup_name}</td>
            <td>
                {if $group.status == 'none'}
                    Nog niet gepland
                {elseif $group.status == 'notmigrated'}
                    Nog niet gemigreerd
                {elseif $group.status == 'queued'}
                    Ingepland
                {elseif $group.status == 'migrating'}
                    Bezig met migreren
                {elseif $group.status == 'migrated'}
                    Gemigreerd
                {elseif $group.status == 'readonly'}
                    Gemigreerd, alleen-lezen
                {elseif $group.status == 'error'}
                    Error
                {/if}
            </td>
            <td>
                <a href="{crmURL p="civicrm/admin/memoria/detail" q="id=`$group.id`"}">Details</a>
                {if in_array($group.status, array('none','notmigrated','error')) } &nbsp;
                    <a href="{crmURL p="civicrm/admin/memoria/do" q="action=migration_confirm&id=`$group.id`"}">Migreren</a>
                {/if}
            </td>
        </tr>
        {foreachelse}
        <tr>
            <td colspan="4">
                De groepen zijn nog niet opgehaald uit Memoria.
                Klik op de knop hierboven om deze te verversen.
            </td>
        </tr>
    {/foreach}
</table>