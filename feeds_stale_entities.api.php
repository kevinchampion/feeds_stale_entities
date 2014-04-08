<?php

/**
 * @file
 * Documentation of Feeds Stale Entities hooks.
 */


/**
 * Hook to declare importers for feeds_stale_entities to monitor. Modules
 * wanting to use hook_feeds_stale_entities() must implement this hook and pass
 * the name/s of the importer/s it would like feeds_stale_entities to monitor
 * for stale entities.
 *
 * This prevents importers that do not need this functionality and will not be
 * hooked into in a module from being monitored and batch queues being created
 * for their stale entities.
 *
 * @param $importer_ids
 *   Associative array of importer ids. For feeds_stale_entities to monitor
 *   importers, the importer id must be explicitly returned using this hook.
 *   The 'refresh' parameter determines whether or not the queue should be
 *   deleted before new items are added.
 */
function hook_feeds_stale_entities_info_alter(&$importer_ids) {

  $importer_ids['my_importer'] = array(
    'importer_id' => 'my_importer',
    'refresh' => TRUE,
  );

}

/**
 * Hook to find stale entities and pass them along for processing. When the data
 * source of a Feeds importer changes, there are instances where it's helpful to
 * know if data has been removed that was previously imported via that importer.
 * This hook is invoked to pass along any entity ids for entities that were
 * imported via a Feeds importer, but no longer exist in the importer source.
 *
 * @param $import_id
 *   String id of the Feeds importer.
 * @param $entity_ids
 *   Array of entity ids that are no longer present in the data source but have
 *   previously been imported from this importer. These are passed in batches
 *   from a queue, so this hook will be invoked whenever there are items in a
 *   queue and feeds_stale_entities_cron is run. Each importer has its own
 *   queue and batches are chunked in sets of 20 entities.
 */
function hook_feeds_stale_entities($import_id, $entity_ids) {

  if ($import_id != 'my_importer') {
    return;
  }

  // We don't need to worry about batching these because the hook is already
  // invoked per batch.
  $nodes = node_delete_multiple($entity_ids);

}
