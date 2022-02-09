/* global jQuery, yii, Swiper, kanbanBaseUrl: false */

humhub.module('kanban', function (module, require, $) {
    var Widget = require('ui.widget').Widget;
    var action = require('action');

    var Assignment = Widget.extend();
    var Task = Widget.extend();

    Task.prototype.init = function () {

    };

    Assignment.prototype.init = function () {
    };

    var addAssignee = function (evt) {
        var $this = evt.$target, $assignees = $this.closest('.kanban-task-assignees').find('.dropdown-toggle'),
            id = $this.data('id');

        var name = $this.data('name') || '', image = $this.data('image') || '';
        var img;
        if (image) {
            img = '<img src="' + image + '" class="rounded-circle mr-1" alt="' + name + '" title="' + name + '">';
        } else {
            img = '<span class="kanban-visualisation mr-1" title="' + name + '">' + name.substr(0, 1).toUpperCase() + '</span>';
        }
        var $assignee = $('<span class="kanban-user" data-id="' + id + '">' + '<input type="hidden" name="assignees[]" value="' + id + '">' + img + '</span>');
        $assignees.append($assignee);

        $this.addClass('is-assigned').css('display', 'none');
        $this.closest('.dropdown-menu').find('.remove-assignee[data-id="' + id + '"]')
            .addClass('is-assigned').css('display', '');
    };
    var removeAssignee = function (evt) {
        var $this = evt.$target, $assignees = $this.closest('.kanban-task-assignees').find('.dropdown-toggle'),
            id = $this.data('id');
        $assignee = $assignees.find('.kanban-user[data-id="' + id + '"]');

        $assignee.remove();
        $this.removeClass('is-assigned').css('display', 'none');
        $this.closest('.dropdown-menu').find('.add-assignee[data-id="' + id + '"]')
            .removeClass('is-assigned').css('display', '');
    };

    module.export({
        Assignment: Assignment,
        Task: Task,
        addAssignee: addAssignee,
        removeAssignee: removeAssignee
    });
});
