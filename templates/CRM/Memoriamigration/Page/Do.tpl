<div class="action-link">
    <a href="{crmURL p="civicrm/admin/memoria"}" class="button"><span>
            <div class="icon back-icon"></div> Terug</span></a>
</div>

{if $action == 'migration_confirm'}

    <p>Je gaat de gebruikersgroep <strong>{$group->usergroup_name}</strong> migreren van Memoria naar CiviCRM.<br />
        Dat betekent het volgende:
    </p>

    <ul class="tomaat">
        <li>Alle inhoud van wat tot nu toe landelijke eigenschappen waren (beroep en bedrijf, belangrijke vrijwilligersactiviteiten) wordt opgenomen op de nieuwe tabbladen 'Werk en interesses' en 'Actief SP'.</li>
        <li>Alle <em>{$details.propertyCount}</em> eigenschappen die de gebruikersgroep zelf heeft toegevoegd, worden als groep gemigreerd.</li>
        <li>Alle <em>{$details.manselCount}</em> handmatige selecties worden eveneens als groep gemigreerd.</li>
        <li>Alle opmerkingen over <em>{$details.commentCount}</em> leden worden opgeslagen als één archiefopmerking per lid, inclusief schrijversnamen en data.</li>
        <li>Bewaarde zoekacties zijn niet 'na te doen' en zullen opnieuw aangemaakt moeten worden.</li>
        <li style="margin-bottom:25px;">Na een succesvolle migratie kan Memoria voor deze groep gebruikers 'read-only' gemaakt worden.</li>

        <li>Hieronder kun je er voor kiezen een of meer van de huidige gebruikers meteen toegang te geven tot CiviCRM. Zij ontvangen een automatische mail met een nieuw wachtwoord. Je moet hierbij wel opgeven tot welk afdelings/regiogebied zij toegang krijgen. [kijken hoeveel werk dat is. groepen zitten op afdelingsniveau ingesteld, dus dat is gelijk geregeld.]</li>
        <li>Als je klaar bent, klik je op Migratie inplannen. De migratie wordt dan uitgevoerd bij de eerstvolgende run van de cronjob MemoriaMigration.Run.</li>
    </ul>

    <div class="action-link">
        <a href="{crmURL p="civicrm/admin/memoria/migration" q="id=`$group->id`"}" class="button"><span>
            <div class="icon add-icon"></div> Migratie inplannen</span></a>
    </div>


{elseif $action == 'migration'}

    <p>De migratie van de groep <strong>{$group->usergroup_name}</strong> is nu ingepland en zal een van de volgende
        keren dat de cronjob draait uitgevoerd worden.</p>

    <p>Je kunt de status en log, inclusief eventuele foutmeldingen, terugvinden
        <a href="{crmURL p="civicrm/admin/memoria/detail" q="id=`$group->id`"}">op de detailpagina</a>
        onder het veld 'Opmerkingen'.</p>

{elseif $action == 'readonly'}

    <p>De gemigreerde groep <strong>{$group->usergroup_name}</strong> heeft vanaf nu alleen nog leesrechten in Memoria.</p>

{else}

    <p>Dit is geen geldige actie.</p>

{/if}