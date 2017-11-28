function rowaction(value, row, index) {
  var output = '<div class="form-inline" style="white-space: nowrap !important;" role="form">';
    if(row.ENABLED == 1) {
      output = output + '<button class="btn btn-warning" data-toggle="modal" href="d.php?a=disable&i=' + row.ID +'" data-target="#modal" data-title="tooltip" data-placement="bottom" title="Disable"><i class="fa fa-toggle-on"></i></button>&nbsp;';
    } else {
      output = output + '<button class="btn btn-success" data-toggle="modal" href="d.php?a=enable&i=' + row.ID +'" data-target="#modal" data-title="tooltip" data-placement="bottom" title="Enable"><i class="fa fa-toggle-off"></i></button>&nbsp;';
    }
    output = output + '<button class="btn btn-default" data-toggle="modal" href="d.php?a=edit&i=' + row.ID +'" data-target="#modal"><i class="fa fa-edit"></i> Edit</button>&nbsp;';
    output = output + '<button class="btn btn-danger" data-toggle="modal" href="d.php?a=del&i=' + row.ID +'" data-target="#modal"><i class="fa fa-trash"></i></button>';
    output = output + '</div>';
  return output;
}

function rowstate(value, row, index) {
  var output = '<div class="status-icon grad-invalid" data-title="tooltip" data-placement="bottom" title="NO STATE"></div>';
  if(row.ENABLED == 0) {
    if(row.STATE == 'WAITING') {
      output = '<div class="status-icon grad-warning" data-title="tooltip" data-placement="bottom" title="WAITING"></div>';
    } else {
      output = '<div class="status-icon grad-invalid" data-title="tooltip" data-placement="bottom" title="DISABLED"></div>';
    }
  } else {
    if(row.STATE == 'JOINED') {
      if(row.MUTE == '1') {
        output = '<div class="status-icon grad-danger" data-title="tooltip" data-placement="bottom" title="MUTED"></div>';
      } else {
        output = '<div class="status-icon grad-primary" data-title="tooltip" data-placement="bottom" title="JOINED"></div>';
      }
    } else {
      if(row.STATE == 'MODDED') {
        output = '<div class="status-icon grad-success" data-title="tooltip" data-placement="bottom" title="MODDED"></div>';
      } else {
        output = '<div class="status-icon grad-warning" data-title="tooltip" data-placement="bottom" title="WAITING"></div>';
      }
    }
  }
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