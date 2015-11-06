<div class="action-link">
    <a href="{crmURL p="civicrm/admin/memoria"}" class="button"><span>
            <div class="icon back-icon"></div> Terug</span></a>
</div>

<p>Je gaat de gebruikersgroep <strong>{$group->usergroup_name}</strong> migreren van Memoria naar CiviCRM.<br/>
    Kies of je een test- of live-migratie wilt uitvoeren, en aan welke afdeling, regio of provincie je de groepen van
    deze gebruikersgroep wilt koppelen. Lees de toelichting hieronder voor meer informatie.</p>

{foreach from=$elementNames item=elementName}
    <div class="crm-section">
        <div class="label">{$form.$elementName.label}</div>
        <div class="content">{$form.$elementName.html}</div>
        <div class="clear"></div>
    </div>
{/foreach}

<div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

<hr/>
<p>
    <strong>Wat gaat er dan gebeuren?</strong><br/>
    <br/>
    - Als je de migratie start, worden de gegevens van <em>{$detail.memberCount}</em> leden geimporteerd in
    CiviCRM.<br/>
    - Een testmigratie kun je meerdere keren uitvoeren, maar kan leiden tot dubbele of verouderde data.<br/>
    Bij een live-migratie worden de` geïmporteerde leden en de gegevens van deze gebruikersgroep in Memoria alleen-lezen
    gemaakt en zullen ze niet nogmaals worden geimporteerd.<br/>
    - Alle inhoud uit de 'Landelijke eigenschappen' wordt ondergebracht op de tabbladen 'Werk en interesses' en 'Actief
    SP'.<br/>
    - De <em>{$detail.propertyCount}</em> eigenschappen die de gebruikersgroep zelf heeft toegevoegd, worden als
    groep gemigreerd. Hetzelfde geldt voor <em>{$detail.manselCount}</em> handmatige selecties. Deze groepen gaan vallen
    onder de afdeling of regio die je hierboven selecteert.<br/>
    - De opmerkingen bij <em>{$detail.commentCount}</em> leden worden opgeslagen als één archiefopmerking per lid,
    inclusief schrijversnamen en data.<br/>
    - Bewaarde zoekacties zijn niet 'na te maken' en zullen opnieuw aangemaakt moeten worden.<br/>
    - Je moet op dit moment nog zelf voor de betreffende afdelingsgebruikers accounts aanmaken, hen toevoegen aan de
    groep
    'Afdelingsgebruikers CiviCRM', en hun toegangsrechten instellen op het tabblad Toegangsgegevens.<br/>
    - De migratie-cronjob wordt standaard ieder uur uitgevoerd en loopt dan alle wachtende migraties af.<br/>
    Bekijk voor meer informatie het <a href="{crmURL p="civicrm/admin/joblog" q="jid=`$cronjobId`"}">cronjob-log</a>.
</p>