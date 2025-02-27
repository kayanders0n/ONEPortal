function collectStartsData(data, rpt_months, employee_list, total_processed, total_errors) {

    $.each(data, function (key, item) {

        var rpt_month = item.item.rpt_month;
        var employee_id = item.item.employee_id;
        var employee_name = item.item.employee_name;
        var employee_color = item.item.employee_color;

        var processed_count = item.item.starts_processed;
        var error_count = item.item.error_count;

        var idx = 0;
        var pidx = 0;

        idx = rpt_months.indexOf(rpt_month);
        if (idx == -1) {
            rpt_months.push(rpt_month);
        }

        idx = rpt_months.indexOf(rpt_month);

        if (!total_processed[idx]) {
            total_processed[idx] = 0;
        }
        total_processed[idx] += processed_count;

        if (!total_errors[idx]) {
            total_errors[idx] = 0;
        }
        total_errors[idx] += error_count;

        idx = -1;
        for (var i = 0; i < employee_list.length; i++) {
            if (employee_list[i].id == employee_id) {
                idx = i;
                break;
            }
        }

        if (idx == -1) {
            var employee = {id: employee_id, name: employee_name, color: employee_color, periods: []};
            var period = {period: rpt_month, total: processed_count, errors: error_count};
            employee.periods.push(period);
            employee_list.push(employee);
        } else {
            pidx = -1;
            for (var i = 0; i < employee_list[idx].periods.length; i++) {
                if (employee_list[idx].periods[i].period == rpt_month) {
                    pidx = i;
                    break;
                }
            }

            // need to handle duplicate periods for same employee, masked employee name
            if (pidx == -1) {
                var period = {period: rpt_month, total: processed_count, errors: error_count};
                employee_list[idx].periods.push(period);
            } else {
                employee_list[idx].periods[pidx].total += processed_count;
                employee_list[idx].periods[pidx].error += error_count;
            }
        }
    });
}