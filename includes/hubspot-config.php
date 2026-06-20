<?php
/**
 * HubSpot funnel config — IDs created via the API for the VT client funnel.
 * Returned as an array so any entry point can `include` it.
 */
return [
    'hub_id'      => '46221241',
    'pipeline_id' => '911878028',          // deal pipeline "VT Client Onboarding"
    'forms'       => [                      // intent => HubSpot form GUID
        'buyers-checklist' => '237eccba-6bc3-42bc-960a-a1588703e03d',
        'practice-audit'   => 'ae0edf4c-ddd7-4beb-b95e-92e9ca515dd4',
        'strategy-call'    => '55e38d22-6262-4b8d-8d07-1dc2ddd07e5f',
        'contact'          => 'd3eb85c3-0d82-412b-b8ad-11cd90efa551',
        'careers'          => 'ade5b120-8be9-4170-8736-e62105ac2b0e',
        'vt-request'       => '497e0c5b-6cac-4424-afb4-69a7b26b79df',
        'roi'              => '66175951-e1e3-40c0-bc49-a9374839d103',
    ],
];
