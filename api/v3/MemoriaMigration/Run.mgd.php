<?php
// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return [
  0 =>
    [
      'name'   => 'Cron:MemoriaMigration.Run',
      'entity' => 'Job',
      'params' =>
        [
          'version'       => 3,
          'name'          => 'Memoria Migration',
          'description'   => 'Voert een migratie van Memoria naar CiviCRM uit voor geselecteerde afdelingen.',
          'run_frequency' => 'Hourly',
          'api_entity'    => 'MemoriaMigration',
          'api_action'    => 'Run',
          'parameters'    => '',
        ],
    ],
];