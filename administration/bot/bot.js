function rowcontrol(value, row, index) {
  var output = '<div class="form-inline" style="white-space: nowrap !important;" role="form">';
  if(row.ENABLED == 1) {
    if(row.STATE == 'RUNNING') {
      output = output + '<button class="btn btn-default" data-toggle="modal" href="d.php?a=restart&i=' + row.ID +'" data-target="#modal" data-title="tooltip" data-placement="bottom" title="Restart"><i class="fa fa-retweet"></i></button>&nbsp;';
      output = output + '<button class="btn btn-danger" data-toggle="modal" href="d.php?a=stop&i=' + row.ID +'" data-target="#modal" data-title="tooltip" data-placement="bottom" title="STOP"><i class="fa fa-power-off"></i></button>&nbsp;';
    } else {
      output = output + '<button class="btn btn-success" data-toggle="modal" href="d.php?a=start&i=' + row.ID +'" data-target="#modal" data-title="tooltip" data-placement="bottom" title="START"><i class="fa fa-power-off"></i></button>&nbsp;';
    }
  }
  output = output + '</div>';
  return output;
}

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
  if(typeof row.STATE != 'undefined') {
    if(row.STATE == 'RUNNING') {
      output = '<div class="status-icon grad-success" data-title="tooltip" data-placement="bottom" title="' + row.STATE + '"></div>';
    } else {
      if(row.ENABLED == 0) {
        output = '<div class="status-icon grad-invalid" data-title="tooltip" data-placement="bottom" title="DISABLED"></div>';
      } else {
        output = '<div class="status-icon grad-danger" data-title="tooltip" data-placement="bottom" title="' + row.STATE + '"></div>';
      }
    }
  } else {
    if(row.ENABLED == 0) {
      output = '<div class="status-icon grad-invalid" data-title="tooltip" data-placement="bottom" title="DISABLED"></div>';
    } else {
      output = '<div class="status-icon grad-danger" data-title="tooltip" data-placement="bottom" title="STOPPED"></div>';
    }
  }
  return output;
}

function rowautostart(value, row, index) {
  var output = ' ';
  if(row.AUTOSTART == 1) {
    output = '<i class="fa fa-refresh" data-title="tooltip" data-placement="bottom" title="Autostart"></i>';
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