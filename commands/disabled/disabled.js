function rowaction(value, row, index) {
  var output = '<div class="form-inline" style="white-space: nowrap !important;" role="form">';
    if(row.ENABLED == 1) {
      output = output + '<button class="btn btn-warning" data-toggle="modal" href="d.php?a=disable&i=' + row.ID +'" data-target="#modal" data-title="tooltip" data-placement="bottom" title="Disable"><i class="fa fa-toggle-on"></i></button>&nbsp;';
    } else {
      output = output + '<button class="btn btn-success" data-toggle="modal" href="d.php?a=enable&i=' + row.ID +'" data-target="#modal" data-title="tooltip" data-placement="bottom" title="Enable"><i class="fa fa-toggle-off"></i></button>&nbsp;';
    }
    output = output + '</div>';
  return output;
}

function rowStyle(row, index) {
    if (row.ENABLED == 0) {
      return {
        classes: 'warning'
      };
    }
  return {};
}