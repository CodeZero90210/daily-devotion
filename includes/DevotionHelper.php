<?php
/**
 * Devotion Helper Functions
 */

/**
 * Sort devotions by calendar order (January 1 → December 31), ignoring year
 * 
 * This function sorts an array of devotion records based on month and day only,
 * ensuring January 1 always comes first and December 31 comes last.
 * 
 * @param array $devotions Array of devotion records (must have 'devotion_date' field in YYYY-MM-DD format)
 * @return array Sorted array of devotions
 */
function sortDevotionsByCalendarOrder($devotions) {
    if (empty($devotions)) {
        return $devotions;
    }
    
    // Create a copy to avoid modifying the original array
    $sorted = $devotions;
    
    // Sort using a custom comparison function
    usort($sorted, function($a, $b) {
        // Parse dates - assuming YYYY-MM-DD format
        $dateA = DateTime::createFromFormat('Y-m-d', $a['devotion_date']);
        $dateB = DateTime::createFromFormat('Y-m-d', $b['devotion_date']);
        
        // Handle invalid dates gracefully
        if (!$dateA || !$dateB) {
            return 0;
        }
        
        // Extract month and day for comparison
        $monthA = (int)$dateA->format('m');
        $dayA = (int)$dateA->format('d');
        $monthB = (int)$dateB->format('m');
        $dayB = (int)$dateB->format('d');
        
        // Compare by month first, then by day
        if ($monthA !== $monthB) {
            return $monthA - $monthB;
        }
        
        return $dayA - $dayB;
    });
    
    return $sorted;
}
