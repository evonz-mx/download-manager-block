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

var DLMListItem = function(list_item_data,parent) {
    this.data = {visible:false,selected:false,parent:parent};
    this.element;

    this._construct = (list_item_data) => {
        Object.assign(this.data, list_item_data);
        this.element = DLMList.create_element(this.data).on('click',this.toggle);
    }

    this.toggle = () => {
        let selected = !this.get('selected');
        let url = this.data.url;
        let dl_index = this.data.parent.download_list.indexOf(url);
        this.set('selected', selected);
        if (selected) {
            if (dl_index === -1) {
                this.data.parent.download_list.push(url);
                this.element.find('label').addClass('dlm-selected');
            }
        } else {
            if (dl_index !== -1) {
                this.data.parent.download_list.splice(dl_index,1);
                this.element.find('label').removeClass('dlm-selected');
            }
        }
    }

    this.set = (key,val) => {
        this.data[key] = val;
    }

    this.get = (key) => {
        if (this.data.hasOwnProperty(key)) {
            return this.data[key];
        } else {
            console.error('error: undefined data key ', key);
            return null;
        }
    }

    this.refresh_visibility = function(filters,parent) {
        let visible = (this.get('os_slug') === filters.os.value);
        let changed = !(this.get('visible') === visible);
        let selected = (this.get('selected'));
        let url = this.get('url');
        let dl_index = parent.parent.download_list.indexOf(url);

        if (changed) {
            this.set('visible',visible);
            if (visible) {
                this.element.appendTo(parent.element);
                if (selected) {
                    if (dl_index === -1) {
                        parent.parent.download_list.push(url);
                    }
                }
            } else {
                this.element.appendTo(parent.trash);
                if (dl_index !== -1) {
                    parent.parent.download_list.splice(dl_index,1);
                }
            }
        }
    }

    this._construct(list_item_data);
}

var DLMList = function(...args) {

    this.title;
    this.element;
    this.parent = args[3];
    this.trash;
    this.list_items = [];

    this.filters = {
        os: {
            key: 'os',
            value: null,
            changed: false,
            from: null,
        },
        version: {
            key: 'version',
            value: null,
            changed: false,
            from: null,
        },
    };

    this._construct = function(args_arr) {
        this.title = args_arr[0];
        this.element = args_arr[2];
        let list_items = args_arr[1];
        this.trash = $('<div style="display:none;"></div>');
        for (let key in list_items) {
            this.list_items.push(new DLMListItem(list_items[key],this.parent));
        }
    }

    this.transform_data = function(data) {
        return data;
    }

    /**
     * Function for setting a single filter value,
     * identifiable by key
     */
    this.set_filter = function(key, value) {

        if (this.filters.hasOwnProperty(key) && (value !== this.filters[key].value)) {
            this.filters[key].from = this.filters[key].value;
            this.filters[key].changed = true;
            this.filters[key].value = value;
        }

        this.refresh_visibility();
    }

    /**
     * Iterates through DLMListItems to determine,
     */
    this.refresh_visibility = function() {
        for (index in this.list_items) {
            if (!this.list_items.hasOwnProperty(index)) {
                continue;
            }
            list_item = this.list_items[index];
            list_item.refresh_visibility(this.filters, this);
        }
        for (key in this.filters) {
            if (!this.filters.hasOwnProperty(key)) continue;
            this.filters[key].changed = false;
            this.filters[key].changed_from = null;
        }
    }

    this._construct(args);
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

const DLM = function(element, lists, index) {
    this.index = index;
    this.element = element;
    this.id = element.id;
    this.lists = lists;
    this.download_list = [];

    this.callback = $(element).data('js_callback');
    this.callback_data = $(element).data('js_callback_data');

    this.lists = this.lists.map.call(this.lists, (list) => {
        return new DLMList(list.name, list.data, list.container, this);
    });

    let version_select = this.element.find('.dlm-radio-group[name=\'version-select\']');
    let os_select = this.element.find('select[name=\'os-select\']');
    let download_button = this.element.find('button');

    os_select.data('lists', this.lists).on('change', function(e) {
        for (list of $(this).data('lists')) {
            list.set_filter('os', $(this).val());
        }
    });

    version_select.data('lists', this.lists).on('change', function(e) {
        for (list of $(this).data('lists')) {
            list.set_filter('version', $(this).val());
        }
    });

    this.download_all = function(list) {
        let dl_list = [].concat(list);
        if (!dl_list.length) return;

        function download_next(list) {
            const interval = setInterval(function(list) {
                let download_link = list.pop();

                if(!download_link) {
                    clearInterval(interval);
                    return;
                }
                const a = document.createElement("a");
                a.setAttribute('href', download_link);
                a.setAttribute('download', '');
                a.click();

            }, 100, list);
        };

        download_next(dl_list);
    }

    download_button.data('parent',this).on('click', function(e) {
        let dlm = $(this).data('parent');

        let js_callback = dlm.callback;
        let js_callback_data = dlm.callback_data;
        let js_download_list = dlm.download_list;
        let download_all = dlm.download_all;

        window[js_callback](js_download_list, function() {
            dlm.download_all(dlm.download_list);
        }, js_callback_data);
    });
};

$(document).ready(function() {
    $('.dlm-block').each(function(index) {
        var lists = [];
        var element = $(this);

        var platforms_container = element.find('.dlm-platforms').find('ul');
        var tools_container = element.find('.dlm-tools').find('ul');
        var plugins_container = element.find('.dlm-plugins').find('ul');

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

        new DLM(element, lists, index);
    });
});
