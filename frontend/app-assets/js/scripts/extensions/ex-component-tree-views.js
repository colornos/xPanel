/*=========================================================================================
    File Name: ex-component-tree-views.js
    Description: Bootstrap Tree View
    ----------------------------------------------------------------------------------------
    Item Name: Colornos Admin - Clean Bootstrap 4 Dashboard HTML Template
    Author: Colornos
    Author URL: http://www.colornos.com
==========================================================================================*/
$(document).ready(function () {
  // Load data dynamically from a PHP endpoint
  $.ajax({
    url: 'fetch-database-data.php', // Replace with the correct PHP endpoint URL
    method: 'GET',
    dataType: 'json',
    success: function (data) {
      // Initialize the tree view
      var $searchableTree = $('#searchable-tree').treeview({
        data: data,
        levels: 1,
        onNodeSelected: function (event, node) {
          if (node.text.startsWith('📄')) {
            loadTableInformation(node);
          }
        }
      });

      // Handle search function and results click
      var search = function () {
        var pattern = $('#input-search').val();
        var options = {
          ignoreCase: $('#chk-ignore-case').is(':checked'),
          exactMatch: $('#chk-exact-match').is(':checked'),
          revealResults: $('#chk-reveal-results').is(':checked')
        };
        var results = $searchableTree.treeview('search', [pattern, options]);
        var output = '<p>' + results.length + ' matches found</p>';
        $.each(results, function (index, result) {
          output += '<p class="search-result-item" data-node-id="' + result.nodeId + '">- ' + result.text + '</p>';
        });
        $('#search-output').html(output);

        $('.search-result-item').on('click', function () {
          var nodeId = $(this).data('node-id');
          var node = $searchableTree.treeview('getNode', nodeId);
          $searchableTree.treeview('selectNode', [nodeId]);
          loadTableInformation(node);
        });
      };

      $('#btn-search').on('click', search);
      $('#input-search').on('keyup', search);

      // Clear button action
      $('#btn-clear-search').on('click', function () {
        $searchableTree.treeview('clearSearch');
        $('#input-search').val('');
        $('#search-output').html('');
        // Clear the table details panel
        $('#table-details').html('<p>Select a table from the tree or search results to view its data.</p>');
      });

      function loadTableInformation(node) {
        if (node.text.startsWith('📄')) {
          var tableName = node.text.replace('📄 ', '');
          var databaseNode = $searchableTree.treeview('getParent', node);
          var databaseName = databaseNode.text.replace('📁 ', '');

          if (databaseName) {
            $.ajax({
              url: 'fetch-table-info.php',
              method: 'GET',
              data: { database: databaseName, table: tableName },
              success: function (response) {
                $('#table-details').html(response);
              },
              error: function () {
                $('#table-details').html('<p>Error loading table information.</p>');
              }
            });
          }
        }
      }
    },
    error: function () {
      console.error('Failed to fetch database data.');
    }
  });
});
