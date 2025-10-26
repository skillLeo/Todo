<div class="modal-overlay" id="createTaskOverlay" style="display:none;"></div>

<div class="modal-shell" id="createTaskModal" style="display:none;">
    <div class="modal-card">
        <div class="modal-head">
            <div class="modal-title-block">
                <div class="modal-title">Add New Task</div>
                <div class="modal-sub">One-time task or daily habit / 40-day challenge</div>
            </div>
            <button class="icon-btn" id="closeCreateTaskModal">
                <i class="fa fa-times"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('tasks.store') }}" class="modal-body">
            @csrf

            <div class="form-grid">
                <div class="form-field">
                    <label class="form-label">Title <span class="req">*</span></label>
                    <input name="title" required class="form-input" placeholder="e.g. Fajr prayer / Client meeting / Diet Plan Day 12">
                </div>

                <div class="form-field">
                    <label class="form-label">Type</label>
                    <select name="task_type" id="task_type" class="form-input">
                        <option value="single">Single Task</option>
                        <option value="habit">Habit / Daily / 40 Days</option>
                    </select>
                </div>

                <div class="form-field">
                    <label class="form-label">Repeat Rule</label>
                    <select name="repeat_rule" class="form-input" id="repeat_rule">
                        <option value="none">No Repeat (just once)</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly / Bill</option>
                        <option value="forty_days">40 Days Challenge</option>
                    </select>
                </div>

                <div class="form-field">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-input">
                </div>

                <div class="form-field">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-input">
                </div>

                <div class="form-field">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-input" placeholder="What is this? why important? notes, targets etc."></textarea>
                </div>
            </div>

            <div id="habitExtraWrap" class="habit-extra-grid" style="display:none;">
                <div class="form-field">
                    <label class="form-label">
                        Target Days (for 40 days style challenge)
                    </label>
                    <input type="number" min="1" max="365" name="target_days" class="form-input" placeholder="40">
                </div>
            </div>

            <div class="form-section">
                <div class="form-label-row">
                    <label class="form-label">Reminder</label>
                    <div class="form-hint">We'll just store this preference</div>
                </div>
                <div class="reminder-row">
                    <label class="checkbox-line">
                        <input type="checkbox" name="remind" value="1">
                        <span>Remind me</span>
                    </label>
                    <input type="time" name="remind_at" class="form-input" style="max-width:140px;">
                </div>
            </div>

            <div class="modal-foot">
                <button type="submit" class="primary-btn w-full">
                    <i class="fa fa-save"></i>
                    <span>Save Task</span>
                </button>
            </div>
        </form>
    </div>
</div>
