var DLMSelectedStore = [];

var DLMChangeLog = function() {
    this.trash = $('div').attr('class','trash');
    this.changes = [];
    this.processed = [];
    this.failed = [];

    this.add_change = (change_obj) => {
        this.changes.push(change_obj);
    }

    this.process_changes = () => {

        this.processed = [];
        this.failed = [];

        for (change_obj of this.changes) {

            change_obj.subject; // DLMListItem
            change_obj.type; // visibility, checked
            change_obj.new_state; // true/false
            change_obj.old_state; // true/false

            if (change_obj.type === 'visibility') {
                if (change_obj.new_state) {
                    change_obj.subject.element.appendTo(change_obj.subject.container);
                    // add to dl list (if checked)
                } else {
                    change_obj.subject.element.appendTo(this.trash);
                    // remove from dl list
                }
            }

            if (change_obj.type === 'checked') {
                change_obj.subject.element.addClass('selected');
                // add to dl list (if visible)
            } else {
                change_obj.subject.element.removeClass('selected');
                // remove from dl list
            }
        }
    }
}

var DLMListItem = function(props) {
    this.data = {};
    this.element = null;
    this.is_visible;
    this.is_checked;

    this.create_element = () => {
        let element = $('div');
        this.data;
        return element;
    }

    this.set_checked = (cbool) => {
        if (this.checked === cbool) {
            return;
        }
        this.checked = cbool;
    }

    this.toggle_checked = () => {
        this.set_checked(this.is_checked ? true : false);
    }

    this.bind_data = (key,val) => {
        let true_key =+ 'dlm-';
        this.data[true_key] = val;
    }

    this.fetch_data = (key) => {
        let true_key =+ 'dlm-';
        if (this.data.hasOwnProperty(true_key)) {
            return this.data[true_key];
        } else {
            console.error('error: undefined data key ', key);
            return null;
        }
    }

    this.init = (props) => {
        let input_keys = [];
        let input = {};

        for (prop_key in props) {
            if (props.hasOwnProperty(prop_key)) {
                input[prop_key] = props[prop_key];
                input_keys.push(prop_key);
            }
        }

        this.element = this.create_element();
        this.parent_element = input.container;
    }

    this.refresh_visibility = function(filters) {
        this.is_visible = (this.os_slug === filters.os.value);
    }

    // this.bind_data('parent', null);
    this.element = this.create_element();
}

var DLMList = function(name, data, container, parent_obj) {

    /** get rid of these... */
    this.name = name;
    this.container = container;
    this.parent = parent_obj;
    this.parent_el = parent_obj.el;
    this.parent_id = this.parent_el.attr('id');
    this.data = [];
    /** end get rid of these... */

    this._dlm
    this._dlm_list = this;
    this._dlm_list_items = [];

    this.dom_element = container;
    this.title = name;

    this.filters = {
        os: {
            key: 'os',
            value: null,
            changed: false,
            changed_from: null,
        },
        version: {
            key: 'version',
            value: null,
            changed: false,
            changed_from: null,
        },
    };

    /*
    for (key in data) {
        let transformed_data = this.transform_data(data);
        this._dlm_list_items.push(new DLMListItem(transformed_data));
    }
    */
    this.transform_data = function(data) {
        return data;
    }

    /**
     * Function for setting a single filter value,
     * identifiable by key
     */
    this.set_filter = function(key, value) {
        value = !!value;
        if (this.filters.hasOwnProperty(key) && (value !== !!this.filters[key].value)) {
            this.filters[key].from = this.filters[key].value;            
            this.filters[key].changed = true;
            this.filters[key].value = value;
        }
    }

    /**
     * Function for bulk updating filters
     * 
     * nd object in which prop keys represent the filter
     * to set, and prop vals represent the new value to
     * set it to
     * 
     * @param {*} filters 
     */
    this.bulk_set_filters = function(filters) {
        for (key in filters) {
            this.set_filter(key, filters[key]);
        }
        this.refresh_visibility();
    }

    /**
     * Iterates through DLMListItems to determine,
     * which view states need to change based on
     * filter changes
     */
    this.refresh_visibility = function() {
        for (index in this._dlm_list_items) {
            if (!this._dlm_list_items.hasOwnProperty(index)) {
                continue;
            }
            dlm_list_item = this._dlm_list_items[index];
            dlm_list_item.refresh_visibility(this.filters);
        }
        for (key in this.filters) {
            if (!this.filters.hasOwnProperty(key)) continue;
            this.filters[key].changed = false;
            this.filters[key].changed_from = null;
        }
    }

    /**
     * Returns all list items that are both visible,
     * and currently selected (list to download)
     */
    this.fetch_selected_items= function() {

    }


    /** 
     *  todo: refactor 
     **/

    for (index in data) {
        this.data.push(Object.assign({
            is_checked: false,
            is_visible: false
        }, data[index]));
    }

    /**
     * todo: map data outside of filter
     */



    this.filter = function() {
        var os = this.parent.os_select.val();
        // var version = this.parent.version_select.val();
        var changes = [];
        var parent = this;

        this.data = this.data.map(function(data_obj, index) {
            visible_changed = false;
            checked_changed = false;
            index = index;

            if (!data_obj.hasOwnProperty('is_visible')) {
                data_obj.is_visible = false;
            }

            if (!data_obj.hasOwnProperty('is_checked')) {
                data_obj.is_checked = false;
            }

            if (!data_obj.hasOwnProperty('el')) {
                data_obj.index = index;
                data_obj.el = DLMList.create_element(data_obj);
                visible_changed = true;
            }

            // temp
            data_obj.id = data_obj.dlm_id;
            data_obj.parent = parent;

            var is_visible = (data_obj.os_slug === os);

            if (data_obj.is_visible !== is_visible) {
                data_obj.is_visible = is_visible;
                visible_changed = true;
            }

            /*
            if (data_obj.is_checked !== is_checked) {
                data_obj.is_checked = is_checked;
                data_obj.checked_changed = true;
            }
            */
            
            if (visible_changed) {
                changes.push(data_obj);
            }

            return data_obj;
        });

        this.build_list(changes);
    };

    this.build_list = function(changes) {

        /** change this... */
        if (!changes) {
            this.container.empty();
            changes = this.data;
        }

        for (change of changes) {
            if (this.container.find('#' + change.dlm_id).length) {
                if (!change.is_visible) {
                    change.el.hide();
                }
            } else {
                if (change.is_visible) {
                    change.el.appendTo(this.container);
                    change.el.show();
                }
            }
            if (change.is_visible) {
                change.el.show();
            }            
        }

    };
};

DLMList.create_element = function(data) {
    el = $('<li></li>')
        .attr('id', data.dlm_id)
        .data('index', data.index)
        .data('object', data)
        .append(
            $('<label></label>')
            .attr('class','dlm-checkbox')
            .append(
                $('<a></a>')
                .html(data.title)
                .attr('href',data.url)
            )
            .append(
                $('<div></div>')
                .attr('class', 'dlm-details-list')
            .append(
                $('<div></div>')
                .attr('class','dlm-details-item')
                .append($('<strong></strong>')
                .html('Version: '))
                .append($('<span></span>')
                .html(data.version))
            )
            .append(
                $('<div></div>')
                .attr('class','dlm-details-item')
                .append($('<strong></strong>')
                .html('MD5: '))
                .append($('<span></span>')
                .html(data.md5))
            )
            .append(
                $('<div></div>')
                .attr('class','dlm-details-item')
                .append($('<strong></strong>')
                .html('Size: '))
                .append($('<span></span>')
                .html(data.size))
            )
        )
    );
    return el;
};

var DLM = function(parent_el, lists, index) {
    this.index = index;
    this.el = parent_el;
    this.id = parent_el.id;

    this.version_select = parent_el.find('.dlm-radio-group[name=\'version-select\']');
    this.os_select = parent_el.find('select[name=\'os-select\']');

    this.lists = [];

    for(i=0; i<lists.length; i++) {
        var ls = lists[i];
        console.log('ls', ls);
        this.lists.push(new DLMList(ls.name, ls.data, ls.container, this));
    }

    this.os_select.data('dlm-instance', this);
    this.version_select.data('dlm-instance',this);

    this.os_select.on('change', function() {
        $(this).data('dlm-instance').lists.forEach(function(list) {
            list.filter();
        });
    });
};

DLM.instances = [];

$(document).ready(function() {
    $('.dlm-block').each(function(index) {
        var lists = [];
        var parent_el = $(this);

        var platforms_container = parent_el.find('.dlm-platforms').find('ul');
        var tools_container = parent_el.find('.dlm-tools').find('ul');
        var plugins_container = parent_el.find('.dlm-plugins').find('ul');

        console.log(platforms_container);

        lists.push({
            'name': 'platforms',
            'data': platforms_container.data('list-json'),
            'container': platforms_container
        });

        lists.push({
            'name': 'tools',
            'data': tools_container.data('list-json'),
            'container': tools_container
        });

        lists.push({
            'name': 'plugins',
            'data': plugins_container.data('list-json'),
            'container': plugins_container
        });

        platforms_container.attr('data-list-json', null);
        tools_container.attr('data-list-json', null);
        plugins_container.attr('data-list-json', null);

        // DLM['instances'].push(new DLM(parent_el,lists,index));
        DLM.instances.push(new DLM(parent_el, lists, index));
    });
});