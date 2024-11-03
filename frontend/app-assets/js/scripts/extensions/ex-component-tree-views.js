/*=========================================================================================
    File Name: ex-component-tree-views.js
    Description: Bootstrap Tree View
    ----------------------------------------------------------------------------------------
    Item Name: Modern Admin - Clean Bootstrap 4 Dashboard HTML Template
    Author: PIXINVENT
    Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/
$(document).ready(function () {
  // Define color variables
  var $primary = '#5A8DEE',
    $danger = '#FF5B5C',
    $warning = '#FDAC41',
    $primary_light = '#6999f3',
    $white = '#fff';

  // Load data dynamically from a PHP endpoint
  $.ajax({
    url: 'fetch-database-data.php', // A PHP script that returns JSON data for the tree
    method: 'GET',
    dataType: 'json',
    success: function (data) {
      // Initialize the searchable tree view with data from the server, collapsed by default
      var $searchableTree = $('#searchable-tree').treeview({
        selectedBackColor: [$primary],
        color: [$primary],
        showBorder: true,
        data: data,
        levels: 1 // This ensures the tree starts with the top-level nodes collapsed
      });

      // Search function for the searchable tree
      var search = function (e) {
        var pattern = $('#input-search').val();
        var options = {
          ignoreCase: $('#chk-ignore-case').is(':checked'),
          exactMatch: $('#chk-exact-match').is(':checked'),
          revealResults: $('#chk-reveal-results').is(':checked')
        };
        var results = $searchableTree.treeview('search', [pattern, options]);
        var output = '<p>' + results.length + ' matches found</p>';
        $.each(results, function (index, result) {
          output += '<p>- ' + result.text + '</p>';
        });
        $('#search-output').html(output);
      }

      // Search button action
      $('#btn-search').on('click', search);
      $('#input-search').on('keyup', search);

      // Clear button action
      $('#btn-clear-search').on('click', function (e) {
        $searchableTree.treeview('clearSearch');
        $('#input-search').val('');
        $('#search-output').html('');
      });
    },
    error: function () {
      console.error('Failed to fetch database data.');
    }
  });
});
