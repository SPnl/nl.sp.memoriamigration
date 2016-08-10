<?php

class CRM_Memoriamigration_Config {

  public static $extName = 'nl.sp.memoriamigration';
  public static $tblName = 'civicrm_migration_memoria';


  public static function get($name) {
    return CRM_Core_BAO_Setting::getItem(self::$extName, $name);
  }

  public static function set($name, $value) {
    CRM_Core_BAO_Setting::setItem($value, self::$extName, $name);
  }

  public static function getCronjobId() {
    return civicrm_api3('Job', 'getvalue', [
      'api_entity' => 'MemoriaMigration',
      'api_action' => 'Run',
      'return'     => 'id',
    ]);
  }

  public static function getPropertyCustomDataMapping() {

    $propertyMapping = [
      33   => ['bedrijfstak', 'agrarische_sector'],
      6    => ['bedrijfstak', 'bouw_of_installatie_inclusief_w'],
      8    => ['bedrijfstak', 'cultuur_sport_vrije_tijd'],
      25   => ['bedrijfstak', 'energie_delfstoffen_milieu'],
      13   => ['bedrijfstak', 'gezondheidszorg'],
      14   => ['bedrijfstak', 'grafische_sector_reclame'],
      15   => ['bedrijfstak', 'handel_verhuur_reparatie'],
      17   => ['bedrijfstak', 'horeca_catering_verblijfsrecrea'],
      7    => ['bedrijfstak', 'ict_telecommunicatie'],
      19   => ['bedrijfstak', 'industrie_productiebedrijf'],
      26   => ['bedrijfstak', 'onderwijs_universiteit_training'],
      1241 => ['bedrijfstak', 'onderzoek_keuring_en_certificer'],
      12   => ['bedrijfstak', 'overheid_gemeente_provincie_rij'],
      32   => ['bedrijfstak', 'transport_en_logistiek'],
      22   => ['bedrijfstak', 'vereniging_stichting_koepelorga'],
      21   => ['bedrijfstak', 'verzorging_welzijn_kinderopvang'],
      9    => ['bedrijfstak', 'zakelijke_dienstverlening_bank_'],
      1259 => ['bedrijfstak', 'anders'],
      1292 => ['beroepsgroep', 'administratief_beroep'],
      1298 => ['beroepsgroep', 'adviseur_consulent_consultant_v'],
      1301 => ['beroepsgroep', 'agrarisch_beroep'],
      1304 => ['beroepsgroep', 'bank_verzekering_belasting_acco'],
      27   => ['beroepsgroep', 'beveiliging_toezicht_politie_de'],
      1310 => ['beroepsgroep', 'bouw_of_installatie'],
      1313 => ['beroepsgroep', 'docent_trainer_onderzoeker'],
      1316 => ['beroepsgroep', 'elektrotechnicus_monteur_elektr'],
      20   => ['beroepsgroep', 'grafisch_journalistiek_media_pr'],
      1265 => ['beroepsgroep', 'horeca_catering'],
      1268 => ['beroepsgroep', 'ict_beroep'],
      1271 => ['beroepsgroep', 'inkoper_verkoper_sales'],
      418  => ['beroepsgroep', 'logistiek_transport_planner'],
      1277 => ['beroepsgroep', 'maatschappelijk_werk_welzijn'],
      1262 => ['beroepsgroep', 'medisch_verzorgend_huishoudelijk'],
      1280 => ['beroepsgroep', 'personeelswerk'],
      1283 => ['beroepsgroep', 'productiemedewerker'],
      1286 => ['beroepsgroep', 'secretaresse_secretaris'],
      2    => ['beroepsgroep', 'staf_management_juridisch'],
      1295 => ['beroepsgroep', 'winkel'],
      1253 => ['hoofdtaak', 'Betaald_werk'],
      10   => ['hoofdtaak', 'Freelance_zelfstandige'],
      30   => ['hoofdtaak', 'Student_scholier'],
      1247 => ['hoofdtaak', 'Huisman_vrouw'],
      1244 => ['hoofdtaak', 'Arbeidsongeschikt'],
      3    => ['hoofdtaak', 'Gepensioneerd'],
      1250 => ['hoofdtaak', 'Vrijwilliger'],
      5    => ['hoofdtaak', 'Werkloos'],
      1256 => ['hoofdtaak', NULL],
      41   => ['lid_van', 'Bewonersorganisatie'],
      76   => ['lid_van', 'Dierenwelzijnsorganisatie'],
      44   => ['lid_van', 'Milieugroepering'],
      43   => ['lid_van', 'OR_MR'],
      46   => ['lid_van', 'Sportvereniging'],
      42   => ['lid_van', 'Vakbond'],
      1340 => ['lid_van', 'Vakbond_actief_'],
      38   => ['actief', 'Niet_actief'],
      39   => ['actief', 'Incidenteel_actief'],
      40   => ['actief', 'Structureel_actief'],
      82   => ['activiteiten', 'Administratief_werk'],
      77   => ['activiteiten', 'Afdelingsbestuur'],
      68   => ['activiteiten', 'Belteam'],
      47   => ['activiteiten', 'Folderen'],
      81   => ['activiteiten', 'Hulpdienst'],
      50   => ['activiteiten', 'Klussen'],
      69   => ['activiteiten', 'Ledenbezoek'],
      71   => ['activiteiten', 'Markt'],
      54   => ['activiteiten', 'Media_website'],
      53   => ['activiteiten', 'Meehelpen_campagnes_acties'],
      70   => ['activiteiten', 'Plakken'],
      58   => ['activiteiten', 'Politieke_basisvorming'],
      48   => ['activiteiten', 'Postverzending'],
      57   => ['activiteiten', 'Raadsfractie'],
      51   => ['activiteiten', 'Rijden'],
      60   => ['activiteiten', 'ROOD'],
      56   => ['activiteiten', 'Specifieke_kunde'],
      49   => ['activiteiten', 'Tribune_rondbrengen'],
      52   => ['activiteiten', 'Wijkcontactpersoon'],
    ];

    $spgf = CRM_Spgeneric_CustomField::singleton();

    $customGroups = [
      // 'actief_land' => $spgf->getGroupByName('Landelijk_actief'),
      'actief_afd' => $spgf->getGroupByName('Actief_SP'),
      'werk_interesses' => $spgf->getGroupByName('SP_kenmerken'),
    ];

    $customFields = [
      'bedrijfstak'  => $spgf->getField('SP_kenmerken', 'Bedrijfstak'),
      'beroepsgroep' => $spgf->getField('SP_kenmerken', 'Beroepsgroep'),
      'hoofdtaak'    => $spgf->getField('SP_kenmerken', 'Hoofdtaak'),
      'lid_van'      => $spgf->getField('SP_kenmerken', 'Lid_van'),
      'actief'       => $spgf->getField('Actief_SP', 'Actief'),
      'activiteiten' => $spgf->getField('Actief_SP', 'Activiteiten'),
    ];

    foreach($customFields as &$customField) {
      $options = civicrm_api3('OptionValue', 'get', ['option_group_id' => $customField['option_group_id']]);
      $customField['options'] = [];
      foreach($options['values'] as $option) {
        $customField['options'][$option['name']] = $option['value'];
      }
    }

    $retPropertyMapping = [];
    foreach($propertyMapping as $property_id => $line) {
      $retPropertyMapping[$property_id] = [
          'custom_' . $customFields[$line[0]]['id'],
          $customFields[$line[0]]['options'][$line[1]],
      ];
    }

    return $retPropertyMapping;
  }

}