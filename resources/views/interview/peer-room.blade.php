@extends('layouts.app')
@section('title', __('peer.room_title'))
@section('head')
    <style>
        html,
        body {
            background: var(--bg) !important;
            color-scheme: dark
        }

        .pr {
            display: flex;
            height: calc(100vh - 64px);
            overflow: hidden
        }

        .pr-hdr {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 16px;
            background: var(--card);
            border-bottom: 1px solid var(--border);
            flex-shrink: 0
        }

        .pr-hdr-l {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0
        }

        .pr-back {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: 0 0;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: .2s;
            flex-shrink: 0
        }

        .pr-back:hover {
            border-color: var(--accent);
            color: var(--accent-2)
        }

        .pr-hdr-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text)
        }

        .pr-hdr-sub {
            font-size: 10px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 1px
        }

        .pr-live {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 7px;
            border-radius: 5px;
            background: rgba(34, 197, 94, .1);
            color: var(--success);
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px
        }

        .pr-live-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--success);
            animation: prP 1.4s ease-in-out infinite
        }

        @keyframes prP {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .3
            }
        }

        .pr-hdr-r {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0
        }

        .pr-tmr {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 5px 10px;
            border-radius: 8px;
            background: var(--bg-2);
            border: 1px solid var(--border)
        }

        .pr-tmr i {
            font-size: 11px;
            color: var(--text-muted)
        }

        .pr-tmr-v {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            letter-spacing: 1px
        }

        .pr-end {
            padding: 5px 10px;
            border-radius: 7px;
            font-size: 10px;
            font-weight: 700;
            border: 1px solid rgba(239, 68, 68, .25);
            background: rgba(239, 68, 68, .06);
            color: var(--danger);
            cursor: pointer;
            transition: .2s
        }

        .pr-end:hover {
            background: rgba(239, 68, 68, .12);
            border-color: rgba(239, 68, 68, .4)
        }

        .pr-code {
            padding: 3px 8px;
            border-radius: 5px;
            background: var(--accent-glow);
            color: var(--accent-2);
            font-size: 10px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
            cursor: pointer;
            transition: .2s
        }

        .pr-code:hover {
            transform: scale(1.05)
        }

        .pr-body {
            display: flex;
            flex: 1;
            min-height: 0
        }

        .pr-left {
            width: 260px;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--border);
            background: var(--bg-2);
            flex-shrink: 0
        }

        .pr-center {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0
        }

        .pr-right {
            width: 320px;
            display: flex;
            flex-direction: column;
            border-left: 1px solid var(--border);
            background: var(--bg-2);
            flex-shrink: 0
        }

        .pr-vid-panel {
            padding: 8px;
            border-bottom: 1px solid var(--border)
        }

        .pr-vid {
            width: 100%;
            aspect-ratio: 16/9;
            border-radius: 8px;
            background: var(--card);
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden
        }

        .pr-vid video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1)
        }

        .pr-vid-off {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 6px;
            color: var(--text-muted)
        }

        .pr-vid-off i {
            font-size: 24px;
            opacity: .4
        }

        .pr-vid-off span {
            font-size: 11px;
            font-weight: 600
        }

        .pr-vid-label {
            position: absolute;
            bottom: 4px;
            left: 4px;
            padding: 2px 6px;
            border-radius: 4px;
            background: rgba(0, 0, 0, .6);
            color: #fff;
            font-size: 9px;
            font-weight: 600;
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            gap: 4px
        }

        .pr-vid-label .dot {
            width: 5px;
            height: 5px;
            border-radius: 50%
        }

        .pr-vid-label .dot.on {
            background: var(--success)
        }

        .pr-vid-label .dot.off {
            background: var(--danger)
        }

        .pr-vid-audio {
            height: 2px;
            border-radius: 1px;
            background: var(--border);
            overflow: hidden;
            margin-top: 3px
        }

        .pr-vid-audio-fill {
            height: 100%;
            background: var(--success);
            border-radius: 1px;
            transition: width .1s;
            width: 0%
        }

        .pr-local-vid {
            width: 100%;
            aspect-ratio: 16/9;
            border-radius: 8px;
            background: var(--card);
            border: 1px solid var(--border);
            position: relative;
            overflow: hidden;
            margin-top: 6px
        }

        .pr-local-vid video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scaleX(-1)
        }

        .pr-ctrls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px;
            border-top: 1px solid var(--border)
        }

        .pr-ctrl {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--bg-elevated);
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: .2s;
            font-size: 13px
        }

        .pr-ctrl:hover {
            border-color: var(--accent);
            color: var(--accent-2)
        }

        .pr-ctrl.on {
            border-color: var(--success);
            color: var(--success);
            background: rgba(34, 197, 94, .08)
        }

        .pr-ctrl.off {
            border-color: rgba(239, 68, 68, .3);
            color: var(--danger);
            background: rgba(239, 68, 68, .06)
        }

        .pr-tabs {
            display: flex;
            border-bottom: 1px solid var(--border);
            background: var(--card);
            flex-shrink: 0
        }

        .pr-tab {
            padding: 8px 16px;
            font-size: 11px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: .2s;
            color: var(--text-muted);
            background: 0 0;
            border-bottom: 2px solid transparent;
            display: flex;
            align-items: center;
            gap: 5px
        }

        .pr-tab:hover {
            color: var(--text-secondary)
        }

        .pr-tab.on {
            color: var(--accent);
            border-bottom-color: var(--accent)
        }

        .pr-tab-badge {
            padding: 1px 6px;
            border-radius: 9px;
            background: var(--accent-glow);
            color: var(--accent-2);
            font-size: 9px;
            font-weight: 700
        }

        .pr-progress {
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
            background: var(--card);
            flex-shrink: 0
        }

        .pr-progress-bar {
            height: 4px;
            border-radius: 2px;
            background: var(--border);
            overflow: hidden;
            margin-bottom: 4px
        }

        .pr-progress-fill {
            height: 100%;
            background: var(--gradient);
            border-radius: 2px;
            transition: width .4s ease
        }

        .pr-progress-text {
            font-size: 10px;
            color: var(--text-muted);
            text-align: center;
            font-weight: 600
        }

        .pr-tasks {
            flex: 1;
            overflow-y: auto;
            padding: 10px
        }

        .pr-tasks::-webkit-scrollbar {
            width: 3px
        }

        .pr-tasks::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px
        }

        .pr-empty {
            text-align: center;
            padding: 2rem 1rem;
            color: var(--text-muted);
            font-size: 12px
        }

        .pr-empty i {
            font-size: 20px;
            color: var(--border);
            margin-bottom: 6px;
            display: block
        }

        .pr-task {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 8px;
            transition: all .25s
        }

        .pr-task:hover {
            border-color: color-mix(in srgb, var(--accent) 30%, var(--border))
        }

        .pr-task.current {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px var(--accent-glow)
        }

        .pr-task.review {
            border-color: var(--warning);
            box-shadow: 0 0 0 2px rgba(234, 179, 8, .15)
        }

        .pr-task.done-task {
            opacity: .7
        }

        .pr-task-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 6px
        }

        .pr-task-num {
            font-size: 9px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .5px
        }

        .pr-task-tags {
            display: flex;
            gap: 4px;
            flex-wrap: wrap
        }

        .pr-tag {
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: .3px
        }

        .pr-tag--code {
            background: rgba(34, 197, 94, .1);
            color: var(--success)
        }

        .pr-tag--theory {
            background: rgba(99, 102, 241, .1);
            color: var(--accent-2)
        }

        .pr-tag--system {
            background: rgba(234, 179, 8, .1);
            color: var(--warning)
        }

        .pr-tag--easy {
            background: rgba(34, 197, 94, .1);
            color: var(--success)
        }

        .pr-tag--medium {
            background: rgba(234, 179, 8, .1);
            color: var(--warning)
        }

        .pr-tag--hard {
            background: rgba(239, 68, 68, .1);
            color: var(--danger)
        }

        .pr-tag--active {
            background: rgba(99, 102, 241, .1);
            color: var(--accent-2)
        }

        .pr-tag--in_progress {
            background: rgba(234, 179, 8, .1);
            color: var(--warning)
        }

        .pr-tag--done {
            background: rgba(34, 197, 94, .1);
            color: var(--success)
        }

        .pr-tag--review {
            background: rgba(234, 179, 8, .15);
            color: var(--warning)
        }

        .pr-tag--skipped {
            background: var(--bg-2);
            color: var(--text-muted)
        }

        .pr-task-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px
        }

        .pr-task-desc {
            font-size: 11px;
            color: var(--text-muted);
            line-height: 1.5;
            white-space: pre-wrap;
            margin-bottom: 8px
        }

        .pr-task-solution {
            margin-top: 8px;
            padding: 8px 10px;
            border-radius: 6px;
            background: var(--bg-2);
            border: 1px solid var(--border)
        }

        .pr-task-solution-label {
            font-size: 9px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .3px;
            margin-bottom: 4px
        }

        .pr-task-solution-text {
            font-size: 11px;
            color: var(--text-secondary);
            white-space: pre-wrap;
            line-height: 1.5
        }

        .pr-task-feedback {
            margin-top: 6px;
            padding: 8px 10px;
            border-radius: 6px;
            background: color-mix(in srgb, var(--accent) 6%, var(--card));
            border: 1px solid color-mix(in srgb, var(--accent) 15%, var(--border))
        }

        .pr-task-feedback-label {
            font-size: 9px;
            font-weight: 700;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: .3px;
            margin-bottom: 3px
        }

        .pr-task-feedback-text {
            font-size: 11px;
            color: var(--text-secondary);
            line-height: 1.5
        }

        .pr-task-score-badge {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            padding: 3px 8px;
            border-radius: 6px;
            background: var(--accent-glow);
            color: var(--accent-2);
            font-size: 11px;
            font-weight: 800
        }

        .pr-task-score-badge i {
            font-size: 9px
        }

        .pr-task-actions {
            display: flex;
            gap: 4px;
            margin-top: 8px;
            flex-wrap: wrap
        }

        .pr-btn {
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 700;
            border: 1px solid var(--border);
            background: var(--bg-2);
            color: var(--text-muted);
            cursor: pointer;
            transition: .2s;
            display: inline-flex;
            align-items: center;
            gap: 4px
        }

        .pr-btn:hover {
            border-color: var(--accent);
            color: var(--accent)
        }

        .pr-btn--accent {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent)
        }

        .pr-btn--accent:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover)
        }

        .pr-btn--success {
            background: var(--success);
            color: #fff;
            border-color: var(--success)
        }

        .pr-btn--success:hover {
            opacity: .9
        }

        .pr-btn--danger {
            border-color: rgba(239, 68, 68, .3);
            color: var(--danger)
        }

        .pr-btn--danger:hover {
            background: rgba(239, 68, 68, .08);
            border-color: var(--danger)
        }

        .pr-solve-form {
            margin-top: 8px
        }

        .pr-solve-textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: var(--bg-2);
            color: var(--text);
            font-size: 11px;
            font-family: 'Courier New', monospace;
            line-height: 1.5;
            resize: vertical;
            min-height: 60px;
            outline: 0;
            box-sizing: border-box
        }

        .pr-solve-textarea:focus {
            border-color: var(--accent)
        }

        .pr-review-form {
            margin-top: 8px;
            padding: 8px 10px;
            border-radius: 6px;
            background: color-mix(in srgb, var(--warning) 5%, var(--card));
            border: 1px solid color-mix(in srgb, var(--warning) 15%, var(--border))
        }

        .pr-review-row {
            display: flex;
            gap: 6px;
            align-items: center;
            margin-bottom: 6px
        }

        .pr-review-label {
            font-size: 9px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .3px;
            min-width: 50px
        }

        .pr-review-input {
            width: 60px;
            padding: 4px 6px;
            border: 1px solid var(--border);
            border-radius: 5px;
            background: var(--bg-2);
            color: var(--text);
            font-size: 12px;
            font-weight: 700;
            outline: 0;
            text-align: center
        }

        .pr-review-input:focus {
            border-color: var(--accent)
        }

        .pr-review-textarea {
            width: 100%;
            padding: 6px 8px;
            border: 1px solid var(--border);
            border-radius: 5px;
            background: var(--bg-2);
            color: var(--text);
            font-size: 11px;
            resize: vertical;
            min-height: 40px;
            outline: 0;
            box-sizing: border-box
        }

        .pr-review-textarea:focus {
            border-color: var(--accent)
        }

        .pr-add {
            padding: 10px;
            border-top: 1px solid var(--border);
            background: var(--card)
        }

        .pr-add-toggle {
            width: 100%;
            padding: 6px;
            border-radius: 6px;
            border: 1px dashed var(--border);
            background: 0 0;
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px
        }

        .pr-add-toggle:hover {
            border-color: var(--accent);
            color: var(--accent)
        }

        .pr-add-form {
            display: none
        }

        .pr-add-form.open {
            display: block
        }

        .pr-add-field {
            margin-bottom: 6px
        }

        .pr-add-label {
            font-size: 9px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: .3px;
            margin-bottom: 2px
        }

        .pr-add-input {
            width: 100%;
            padding: 5px 8px;
            border: 1px solid var(--border);
            border-radius: 5px;
            background: var(--bg-2);
            color: var(--text);
            font-size: 11px;
            outline: 0;
            transition: border-color .2s;
            box-sizing: border-box
        }

        .pr-add-input:focus {
            border-color: var(--accent)
        }

        textarea.pr-add-input {
            resize: vertical;
            min-height: 50px;
            font-family: inherit;
            line-height: 1.5
        }

        .pr-add-row {
            display: flex;
            gap: 4px
        }

        .pr-add-row>* {
            flex: 1
        }

        .pr-add-select {
            width: 100%;
            padding: 5px 8px;
            border: 1px solid var(--border);
            border-radius: 5px;
            background: var(--bg-2);
            color: var(--text);
            font-size: 11px;
            outline: 0
        }

        .pr-add-actions {
            display: flex;
            gap: 4px;
            margin-top: 6px
        }

        .pr-add-btn {
            flex: 1;
            padding: 5px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: 700;
            border: 0;
            cursor: pointer;
            transition: .2s
        }

        .pr-add-btn--save {
            background: var(--accent);
            color: #fff
        }

        .pr-add-btn--save:hover {
            background: var(--accent-hover)
        }

        .pr-add-btn--cancel {
            background: var(--bg-2);
            color: var(--text-muted);
            border: 1px solid var(--border)
        }

        .pr-add-btn--cancel:hover {
            background: var(--card)
        }

        .pr-editor {
            display: flex;
            flex-direction: column;
            border-top: 1px solid var(--border)
        }

        .pr-editor-bar {
            display: flex;
            align-items: center;
            padding: 4px 10px;
            background: var(--bg-elevated);
            border-bottom: 1px solid var(--border);
            gap: 6px;
            flex-shrink: 0
        }

        .pr-editor-dots {
            display: flex;
            gap: 4px
        }

        .pr-editor-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%
        }

        .pr-editor-dot:nth-child(1) {
            background: var(--danger)
        }

        .pr-editor-dot:nth-child(2) {
            background: var(--warning)
        }

        .pr-editor-dot:nth-child(3) {
            background: var(--success)
        }

        .pr-editor-name {
            font-size: 10px;
            color: var(--text-muted);
            font-family: 'Courier New', monospace;
            margin-left: 4px
        }

        .pr-editor-lang {
            margin-left: auto;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid var(--border);
            background: var(--bg);
            color: var(--text-secondary);
            font-size: 10px;
            outline: 0
        }

        .pr-editor textarea {
            flex: 1;
            width: 100%;
            padding: 10px;
            background: 0 0;
            border: 0;
            color: var(--success);
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.6;
            resize: none;
            outline: 0;
            min-height: 150px
        }

        .pr-chat-hdr {
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 6px
        }

        .pr-chat-hdr i {
            color: var(--accent);
            font-size: 12px
        }

        .pr-chat-hdr span {
            font-size: 12px;
            font-weight: 700;
            color: var(--text)
        }

        .pr-chat-msgs {
            flex: 1;
            overflow-y: auto;
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 8px
        }

        .pr-chat-msgs::-webkit-scrollbar {
            width: 3px
        }

        .pr-chat-msgs::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px
        }

        .pr-chat-msg {
            max-width: 85%
        }

        .pr-chat-msg.me {
            align-self: flex-end
        }

        .pr-chat-msg.them {
            align-self: flex-start
        }

        .pr-chat-bubble {
            padding: 6px 10px;
            border-radius: 8px;
            font-size: 11px;
            line-height: 1.5;
            color: var(--text-secondary);
            border: 1px solid var(--border);
            background: var(--bg-elevated);
            word-break: break-word
        }

        .pr-chat-msg.me .pr-chat-bubble {
            background: var(--accent-glow);
            border-color: rgba(99, 102, 241, .15);
            color: var(--accent-2)
        }

        .pr-chat-name {
            font-size: 9px;
            color: var(--text-muted);
            margin-bottom: 1px;
            font-weight: 600
        }

        .pr-chat-msg.me .pr-chat-name {
            text-align: right
        }

        .pr-chat-in {
            padding: 8px 12px;
            border-top: 1px solid var(--border)
        }

        .pr-chat-f {
            display: flex;
            gap: 4px
        }

        .pr-chat-fi {
            flex: 1;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: var(--bg-elevated);
            color: var(--text);
            font-size: 11px;
            outline: 0;
            transition: border-color .2s
        }

        .pr-chat-fi:focus {
            border-color: var(--accent)
        }

        .pr-chat-fi::placeholder {
            color: var(--text-muted)
        }

        .pr-chat-sd {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 0;
            background: var(--gradient);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: .2s;
            box-shadow: 0 2px 6px var(--accent-glow-strong);
            flex-shrink: 0;
            font-size: 12px
        }

        .pr-chat-sd:hover {
            transform: scale(1.06)
        }

        .pr-toast {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(80px);
            padding: 8px 16px;
            border-radius: 8px;
            background: var(--bg-elevated);
            border: 1px solid var(--border);
            color: var(--text);
            font-size: 11px;
            font-weight: 600;
            z-index: 999;
            box-shadow: 0 6px 24px rgba(0, 0, 0, .5);
            transition: transform .3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            pointer-events: none
        }

        .pr-toast.on {
            transform: translateX(-50%) translateY(0)
        }

        .pr-btn:active,
        .pr-ctrl:active,
        .pr-end:active,
        .pr-add-btn:active,
        .pr-chat-sd:active,
        .pr-back:active,
        .pr-code:active {
            transform: scale(.92)
        }

        .pr-chat-msgs,
        .pr-tasks {
            scroll-behavior: smooth
        }

        @keyframes prFadeUp {
            from {
                opacity: 0;
                transform: translateY(10px)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .pr-add-form.open {
            animation: prFadeUp .28s ease
        }

        .pr-task-solution,
        .pr-task-feedback,
        .pr-solve-form,
        .pr-review-form {
            animation: prFadeUp .25s ease
        }

        .pr-chat-msg {
            animation: prFadeUp .25s ease
        }

        @media(prefers-reduced-motion:reduce) {
            * {
                animation: none !important;
                transition: none !important
            }
        }

        @media(max-width:900px) {
            .pr-left {
                width: 200px
            }

            .pr-right {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                width: 100% !important;
                height: 50vh;
                z-index: 50;
                border-left: 0;
                border-top: 1px solid var(--border);
                border-radius: 12px 12px 0 0;
                transform: translateY(100%);
                transition: transform .3s ease
            }

            .pr-right.mob-open {
                transform: translateY(0)
            }
        }
    </style>
@endsection

@section('content')
    <div class="pr" x-data="prRoom()" x-init="init()">
        <div style="display:flex;flex-direction:column;width:100%">
            <div class="pr-hdr">
                <div class="pr-hdr-l">
                    <a href="{{ route('peer.index') }}" class="pr-back"><i class="fas fa-arrow-left"></i></a>
                    <div>
                        <div class="pr-hdr-title">{{ $isHost ? __('interview_role_host') : __('interview_role_member') }} &mdash; {{ $room->room_code }}
                        </div>
                        <div class="pr-hdr-sub">
                            <span class="pr-live"><span class="pr-live-dot"></span> LIVE</span>
                            <span
                                x-text="peerConnected ? '{{ addslashes($peerName ?? ('peer.interlocutor')) }} {{ __('peer.connected') }}' : '{{ __('peer.waiting') }}'"></span>
                        </div>
                    </div>
                </div>
                <div class="pr-hdr-r">
                    <span class="pr-code" @click="copyCode()" title="{{ __('peer.copy_code') }}">{{ $room->room_code }}</span>
                    <button class="pr-end" style="border-color:var(--accent);color:var(--accent)" @click="showInvite = true"><i class="fas fa-user-plus" style="margin-right:3px"></i>
                        {{ __('peer.invite') }}</button>
                    <div class="pr-tmr"><i class="fas fa-clock"></i><span class="pr-tmr-v" x-text="fmt(timer)"></span></div>
                    <button class="pr-end" @click="end()"><i class="fas fa-phone-slash" style="margin-right:3px"></i>
                        {{ __('interview_end') }}</button>
                </div>
            </div>
            <div class="pr-body">
                <div class="pr-left">
                    <div class="pr-vid-panel">
                        <div class="pr-vid">
                            <video x-ref="remoteVideo" autoplay playsinline></video>
                            <div class="pr-vid-off" x-show="!peerConnected"><i class="fas fa-user"></i><span
                                    x-text="peerName || '{{ __('peer.waiting') }}'"></span></div>
                            <div class="pr-vid-label" x-show="peerConnected"><span class="dot on"></span><span
                                    x-text="peerName || '{{ __('peer.interlocutor') }}'"></span></div>
                        </div>
                        <div class="pr-local-vid">
                            <video x-ref="localVideo" autoplay playsinline muted></video>
                            <div class="pr-vid-off" x-show="!localStream" style="font-size:10px"><i
                                    class="fas fa-video-slash" style="font-size:16px"></i><span>{{ __('peer.cam_off') }}</span></div>
                            <div class="pr-vid-label"><span class="dot" :class="localStream ? 'on' : 'off'"></span> {{ __('peer.you') }}</div>
                        </div>
                        <div class="pr-vid-audio" x-show="localStream">
                            <div class="pr-vid-audio-fill" :style="'width:' + localAudio + '%'"></div>
                        </div>
                    </div>
                    <div class="pr-ctrls">
                        <button class="pr-ctrl" :class="micOn ? 'on' : 'off'" @click="toggleMic()"><i
                                :class="micOn ? 'fas fa-microphone' : 'fas fa-microphone-slash'"></i></button>
                        <button class="pr-ctrl" :class="camOn ? 'on' : 'off'" @click="toggleCam()"><i
                                :class="camOn ? 'fas fa-video' : 'fas fa-video-slash'"></i></button>
                        <button class="pr-ctrl" :class="speakerOn ? '' : 'off'" @click="toggleSpeaker()"><i
                                :class="speakerOn ? 'fas fa-volume-up' : 'fas fa-volume-mute'"></i></button>
                        <button class="pr-ctrl" :class="showChat ? 'on' : ''" @click="showChat = !showChat" title="{{ __('interview_chat') }}"><i
                                class="fas fa-comment-dots"></i></button>
                        <button class="pr-ctrl" @click="copyCode()"><i class="fas fa-copy"></i></button>
                    </div>
                    <div style="padding:0 8px 8px">
                        <button class="pr-btn pr-btn--accent" style="width:100%;justify-content:center" @click="showInvite = true"><i class="fas fa-user-plus"></i> {{ __('peer.invite') }}</button>
                    </div>
                </div>
                <div class="pr-center">
                    <div class="pr-tabs">
                        <button class="pr-tab" :class="activeTab === 'tasks' ? 'on' : ''" @click="activeTab = 'tasks'"><i
                                class="fas fa-list-check"></i> {{ __('peer.tasks') }} <span class="pr-tab-badge" x-text="tasks.length"
                                x-show="tasks.length > 0"></span></button>
                        <button class="pr-tab" :class="activeTab === 'code' ? 'on' : ''" @click="activeTab = 'code'"><i
                                class="fas fa-code"></i> {{ __('peer.code_editor') }}</button>
                        <button class="pr-tab" :class="activeTab === 'board' ? 'on' : ''" @click="activeTab = 'board'; $nextTick(() => initBoard())"><i
                                class="fas fa-chalkboard"></i> {{ __('peer.whiteboard') }}</button>
                    </div>
                    @if($isHost)
                        <div class="pr-progress" x-show="tasks.length > 0">
                            <div class="pr-progress-bar">
                                <div class="pr-progress-fill" :style="'width:' + progressPercent + '%'"></div>
                            </div>
                            <div class="pr-progress-text" x-text="progressText"></div>
                        </div>
                    @endif
                    <div class="pr-tasks" x-show="activeTab === 'tasks'" x-transition.opacity.duration.200ms>
                        <template x-if="tasks.length === 0">
                            <div class="pr-empty"><i class="fas fa-clipboard-list"></i>{{ __('peer.no_tasks_add') }}</div>
                        </template>
                        <template x-for="(task, i) in tasks" :key="task.id">
                            <div class="pr-task"
                                :class="{'current': currentTaskId === task.id, 'review': task.status === 'review', 'done-task': task.status === 'done' || task.status === 'skipped'}"
                                @click="currentTaskId = task.id">
                                <div class="pr-task-head">
                                    <span class="pr-task-num" x-text="'#' + (i + 1)"></span>
                                    <div class="pr-task-tags">
                                        <span class="pr-tag" :class="'pr-tag--' + task.difficulty"
                                            x-text="diffLabel(task.difficulty)"></span>
                                        <span class="pr-tag"
                                            :class="'pr-tag--' + task.type.replace('_system','system').split('_')[0]"
                                            x-text="typeLabel(task.type)"></span>
                                        <span class="pr-tag" :class="'pr-tag--' + task.status"
                                            x-text="statusLabel(task.status)"></span>
                                        <template x-if="task.score !== null"><span class="pr-task-score-badge"><i
                                                    class="fas fa-star"></i> <span
                                                    x-text="task.score + '/10'"></span></span></template>
                                    </div>
                                </div>
                                <div class="pr-task-title" x-text="task.title"></div>
                                <div class="pr-task-desc" x-show="task.description" x-text="task.description"></div>
                                @if(!$isHost)
                                    <template x-if="task.starter_code && task.type === 'code'">
                                        <div class="pr-task-actions"><button class="pr-btn" @click.stop="loadTaskCode(task)"><i
                                                    class="fas fa-code"></i> {{ __('peer.load_to_editor') }}</button></div>
                                    </template>
                                    <template x-if="task.solution && (task.status === 'done' || task.status === 'review')">
                                        <div class="pr-task-solution">
                                            <div class="pr-task-solution-label"><i class="fas fa-pen"></i> {{ __('peer.your_answer') }}</div>
                                            <div class="pr-task-solution-text" x-text="task.solution"></div>
                                        </div>
                                    </template>
                                @endif
                                <template x-if="task.feedback">
                                    <div class="pr-task-feedback">
                                        <div class="pr-task-feedback-label"><i class="fas fa-comment"></i> {{ __('interview_feedback') }}</div>
                                        <div class="pr-task-feedback-text" x-text="task.feedback"></div>
                                    </div>
                                </template>
                                @if(!$isHost)
                                    <template x-if="task.status === 'active'">
                                        <div class="pr-task-actions"><button class="pr-btn pr-btn--accent"
                                                @click.stop="startTask(task)"><i class="fas fa-play"></i> {{ __('peer.start_solving') }}</button></div>
                                    </template>
                                    <template x-if="task.status === 'in_progress'">
                                        <div class="pr-solve-form" @click.stop>
                                            <textarea class="pr-solve-textarea" x-model="task._solution"
                                                :placeholder="task.type === 'code' ? '{{ __('peer.paste_solution_code') }}' : '{{ __('peer.enter_answer_placeholder') }}'"
                                                rows="3"></textarea>
                                            <div class="pr-task-actions">
                                                <button class="pr-btn pr-btn--success" @click.stop="submitTask(task)"><i
                                                        class="fas fa-check"></i> {{ __('peer.submit') }}</button>
                                                <button class="pr-btn pr-btn--danger" @click.stop="skipTask(task)"><i
                                                        class="fas fa-forward"></i> {{ __('peer.skip') }}</button>
                                            </div>
                                        </div>
                                    </template>
                                @endif
                                @if($isHost)
                                    <template x-if="task.status === 'done'">
                                        <div class="pr-review-form" @click.stop>
                                            <div class="pr-review-row"><span class="pr-review-label">{{ __('peer.score') }}</span><input
                                                    type="number" class="pr-review-input" min="0" max="10" x-model="task._score"
                                                    placeholder="0-10"><span style="font-size:10px;color:var(--text-muted)">/
                                                    10</span></div>
                                            <textarea class="pr-review-textarea" x-model="task._feedback"
                                                placeholder="{{ __('peer.feedback_for_candidate') }}" rows="2"></textarea>
                                            <div class="pr-task-actions"><button class="pr-btn pr-btn--accent"
                                                    @click.stop="reviewTask(task)"><i class="fas fa-check-double"></i>
                                                    {{ __('peer.rate') }}</button></div>
                                        </div>
                                    </template>
                                    <template x-if="task.status === 'review' && task.score !== null">
                                        <div class="pr-task-actions"><button class="pr-btn pr-btn--danger"
                                                @click.stop="removeTask(task.id)"><i class="fas fa-trash"></i> {{ __('interview_delete') }}</button>
                                        </div>
                                    </template>
                                @endif
                            </div>
                        </template>
                    </div>
                    <div class="pr-editor" x-show="activeTab === 'code'" x-transition.opacity.duration.200ms style="flex:1">
                        <div class="pr-editor-bar">
                            <div class="pr-editor-dots">
                                <div class="pr-editor-dot"></div>
                                <div class="pr-editor-dot"></div>
                                <div class="pr-editor-dot"></div>
                            </div>
                            <span class="pr-editor-name">solution</span>
                            <select class="pr-editor-lang" x-model="codeLang" @change="changeCodeLang()">
                                <option value="python">Python</option>
                                <option value="javascript">JavaScript</option>
                                <option value="java">Java</option>
                                <option value="cpp">C++</option>
                                <option value="c">C</option>
                                <option value="go">Go</option>
                                <option value="rust">Rust</option>
                                <option value="typescript">TypeScript</option>
                            </select>
                        </div>
                        <textarea x-ref="codeEditor" x-model="codeContent" @input="onCodeInput()"
                            placeholder="{{ __('peer.write_solution_here') }}" spellcheck="false"></textarea>
                        <div style="display:flex;gap:6px;padding:8px 10px;border-top:1px solid var(--border);align-items:center;flex-wrap:wrap">
                            <button class="pr-btn pr-btn--success" @click="runCode()" :disabled="codeRunning"><i
                                    :class="codeRunning ? 'fas fa-spinner fa-spin' : 'fas fa-play'"></i> {{ __('peer.run_code') }}</button>
                            <input class="pr-add-input" type="text" x-model="codeStdin" :placeholder="'stdin'"
                                style="flex:1;min-width:120px">
                        </div>
                        <div x-show="codeOutput" style="padding:8px 10px;border-top:1px solid var(--border);max-height:160px;overflow-y:auto">
                            <div class="pr-add-label">stdout</div>
                            <pre x-text="codeOutput" style="font-family:'Courier New',monospace;font-size:11px;color:var(--text-secondary);white-space:pre-wrap;margin:0"></pre>
                            <div x-show="codeError" class="pr-add-label" style="color:var(--danger);margin-top:4px">stderr</div>
                            <pre x-show="codeError" x-text="codeError" style="font-family:'Courier New',monospace;font-size:11px;color:var(--danger);white-space:pre-wrap;margin:0"></pre>
                        </div>
                    </div>
                    <div class="pr-editor" x-show="activeTab === 'board'" x-transition.opacity.duration.200ms style="flex:1">
                        <div class="pr-editor-bar" style="flex-wrap:wrap;row-gap:6px">
                            <div style="display:flex;gap:4px;align-items:center">
                                <button class="pr-btn" :class="boardTool === 'pen' ? 'pr-btn--accent' : ''" @click="boardTool = 'pen'; boardEraser = false" :title="'{{ __('peer.tool_pen') }}'"><i class="fas fa-pen"></i></button>
                                <button class="pr-btn" :class="boardTool === 'line' ? 'pr-btn--accent' : ''" @click="boardTool = 'line'; boardEraser = false" :title="'{{ __('peer.tool_line') }}'"><i class="fas fa-minus"></i></button>
                                <button class="pr-btn" :class="boardTool === 'arrow' ? 'pr-btn--accent' : ''" @click="boardTool = 'arrow'; boardEraser = false" :title="'{{ __('peer.tool_arrow') }}'"><i class="fas fa-arrow-right"></i></button>
                                <button class="pr-btn" :class="boardTool === 'rect' ? 'pr-btn--accent' : ''" @click="boardTool = 'rect'; boardEraser = false" :title="'{{ __('peer.tool_rect') }}'"><i class="far fa-square"></i></button>
                                <button class="pr-btn" :class="boardTool === 'circle' ? 'pr-btn--accent' : ''" @click="boardTool = 'circle'; boardEraser = false" :title="'{{ __('peer.tool_circle') }}'"><i class="far fa-circle"></i></button>
                                <button class="pr-btn" :class="boardTool === 'text' ? 'pr-btn--accent' : ''" @click="boardTool = 'text'; boardEraser = false" :title="'{{ __('peer.tool_text') }}'"><i class="fas fa-font"></i></button>
                                <button class="pr-btn" :class="boardEraser ? 'pr-btn--accent' : ''" @click="boardEraser = !boardEraser" :title="'{{ __('peer.tool_eraser') }}'"><i class="fas fa-eraser"></i></button>
                            </div>
                            <div style="display:flex;gap:4px;align-items:center">
                                <template x-for="c in boardColors" :key="c">
                                    <button @click="boardColor = c; boardEraser = false" :style="'width:22px;height:22px;border-radius:50%;background:' + c + ';border:' + (boardColor === c && !boardEraser ? '2px solid var(--accent)' : '1px solid var(--border)') + ';cursor:pointer'"></button>
                                </template>
                            </div>
                            <input type="range" min="1" max="12" x-model="boardWidth" style="width:70px" title="width">
                            <button class="pr-btn" @click="undoBoard()" :disabled="!boardUndo.length" title="Undo"><i class="fas fa-undo"></i></button>
                            <button class="pr-btn" :class="boardGrid ? 'pr-btn--accent' : ''" @click="boardGrid = !boardGrid; redrawBoard()" title="Grid"><i class="fas fa-th"></i></button>
                            <button class="pr-btn" @click="exportBoard()" title="PNG"><i class="fas fa-download"></i></button>
                            <button class="pr-btn pr-btn--danger" @click="clearBoard()"><i class="fas fa-trash"></i> {{ __('peer.board_clear') }}</button>
                            <span style="margin-left:auto;font-size:10px;color:var(--text-muted)" x-text="(boardStrokes.length || 0) + ' · ' + (boardSynced ? '{{ __('peer.board_synced') }}' : '{{ __('peer.board_syncing') }}')"></span>
                        </div>
                        <div style="flex:1;position:relative;min-height:300px;display:flex">
                            <canvas x-ref="boardCanvas" style="flex:1;width:100%;min-height:300px;background:var(--bg);cursor:crosshair;touch-action:none"></canvas>
                            <input x-ref="boardTextInput" x-model="boardTextValue" x-show="boardTextOpen" @keydown.enter="commitBoardText()" @keydown.escape="boardTextOpen = false"
                                style="position:absolute;padding:4px 8px;border:1px dashed var(--accent);background:var(--bg-elevated);color:var(--text);font-size:14px;border-radius:4px;outline:0;min-width:120px" />
                        </div>
                    </div>
                    <div class="pr-add" x-show="activeTab === 'tasks' && isHost">
                        <button class="pr-add-toggle" @click="showAddForm = !showAddForm" x-show="!showAddForm"><i
                                class="fas fa-plus"></i> {{ __('peer.add_task') }}</button>
                        <div class="pr-add-form" :class="showAddForm ? 'open' : ''">
                            <div class="pr-add-field">
                                <div class="pr-add-label">{{ __('peer.title_label') }}</div><input class="pr-add-input" type="text"
                                    x-model="newTask.title" placeholder="{{ __('peer.example_doubly_linked_list') }}">
                            </div>
                            <div class="pr-add-field">
                                <div class="pr-add-label">{{ __('peer.description') }}</div><textarea class="pr-add-input"
                                    x-model="newTask.description" placeholder="{{ __('peer.task_description') }}"></textarea>
                            </div>
                            <div class="pr-add-row">
                                <div class="pr-add-field">
                                    <div class="pr-add-label">{{ __('peer.type') }}</div><select class="pr-add-select" x-model="newTask.type">
                                        <option value="code">{{ __('peer.code') }}</option>
                                        <option value="theory">{{ __('peer.theory') }}</option>
                                        <option value="system_design">{{ __('peer.design') }}</option>
                                    </select>
                                </div>
                                <div class="pr-add-field">
                                    <div class="pr-add-label">{{ __('peer.difficulty') }}</div><select class="pr-add-select"
                                        x-model="newTask.difficulty">
                                        <option value="easy">{{ __('difficulty_easy') }}</option>
                                        <option value="medium">{{ __('difficulty_medium') }}</option>
                                        <option value="hard">{{ __('difficulty_hard') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="pr-add-field" x-show="newTask.type === 'code'">
                                <div class="pr-add-label">{{ __('peer.language_starter_code') }}</div><input class="pr-add-input" type="text"
                                    x-model="newTask.language" placeholder="python" style="margin-bottom:4px"><textarea
                                    class="pr-add-input" x-model="newTask.starter_code"
                                    placeholder="def solve():&#10;    pass"
                                    style="font-family:'Courier New',monospace;font-size:11px"></textarea>
                            </div>
                            <div class="pr-add-actions"><button class="pr-add-btn pr-add-btn--cancel"
                                    @click="showAddForm = false">{{ __('peer.cancel') }}</button><button class="pr-add-btn pr-add-btn--save"
                                    @click="addTask()"><i class="fas fa-plus" style="margin-right:2px"></i>
                                    {{ __('peer.add') }}</button></div>
                        </div>
                    </div>
                </div>
                <div class="pr-right" :class="showChat ? 'mob-open' : ''" x-show="showChat" x-transition.opacity.duration.250ms>
                    <div class="pr-chat-hdr"><i class="fas fa-comment-dots"></i><span>{{ __('interview_chat') }}</span></div>
                    <div class="pr-chat-msgs" x-ref="chatBox">
                        <template x-for="(m, i) in messages" :key="m.id || i">
                            <div class="pr-chat-msg" :class="m.user_id == currentUserId ? 'me' : 'them'">
                                <div class="pr-chat-name" x-text="m.user_id == currentUserId ? '{{ __("peer.you") }}' : m.user_name"></div>
                                <div class="pr-chat-bubble">
                                    <template x-if="m.message_type === 'image' && m.file_url">
                                        <div style="margin-bottom:6px"><img :src="fixUrl(m.file_url)"
                                                style="max-width:100%;max-height:200px;border-radius:6px;cursor:pointer"
                                                @click="openFile(m.file_url)"></div>
                                    </template>
                                    <template x-if="m.message_type === 'video' && m.file_url">
                                        <div style="margin-bottom:6px"><video :src="fixUrl(m.file_url)" controls
                                                style="max-width:100%;max-height:200px;border-radius:6px"></video></div>
                                    </template>
                                    <template x-if="m.message_type === 'audio' && m.file_url">
                                        <div style="margin-bottom:6px"><audio :src="fixUrl(m.file_url)" controls
                                                style="max-width:100%"></audio></div>
                                    </template>
                                    <template x-if="m.message_type === 'file' && m.file_url">
                                        <div
                                            style="margin-bottom:6px;padding:6px 8px;border-radius:6px;border:1px solid var(--border);display:flex;align-items:center;gap:6px">
                                            <i class="fas fa-file" style="font-size:14px;color:var(--text-muted)"></i>
                                            <div style="flex:1;min-width:0">
                                                <a :href="fixUrl(m.file_url)" target="_blank"
                                                    style="font-size:11px;color:var(--accent);text-decoration:none"
                                                    x-text="m.file_name || 'File'"></a>
                                                <div style="font-size:9px;color:var(--text-muted)"
                                                    x-text="fmtSize(m.file_size)"></div>
                                            </div>
                                        </div>
                                    </template>
                                    <span x-html="linkify(m.text)"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div x-show="chatPreviewFile"
                        style="padding:6px 12px;border-top:1px solid var(--border);display:flex;align-items:center;gap:6px;background:var(--bg-2)">
                        <i class="fas fa-paperclip" style="color:var(--accent);font-size:11px"></i>
                        <span
                            style="font-size:11px;color:var(--text-secondary);flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                            x-text="chatPreviewFile?.name"></span>
                        <button @click="chatPreviewFile=null"
                            style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:12px"><i
                                class="fas fa-times"></i></button>
                    </div>
                    <div class="pr-chat-in">
                        <div class="pr-chat-f">
                            <label
                                style="width:32px;height:32px;border-radius:6px;border:1px solid var(--border);background:var(--bg-elevated);color:var(--text-muted);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:.2s;flex-shrink:0;font-size:12px"
                                title="{{ __('peer.attach_file') }}"><i class="fas fa-paperclip"></i><input type="file"
                                    style="display:none" @change="handleChatFile($event)"
                                    accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.txt,.zip"></label>
                            <input class="pr-chat-fi" type="text" x-model="chatInput" @keydown.enter="sendChat()"
                                placeholder="{{ __('peer.message_placeholder') }}">
                            <button class="pr-chat-sd" @click="sendChat()"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pr-toast" :class="toastShow ? 'on' : ''"><i :class="toastIcon" :style="'color:' + toastColor"></i><span
                x-text="toastMsg"></span></div>
        <div x-show="showInvite" x-transition.opacity.duration.200ms style="position:fixed;inset:0;z-index:300;display:flex;align-items:center;justify-content:center;padding:20px" @click.self="showInvite = false">
            <div style="position:absolute;inset:0;background:rgba(0,0,0,.6)" @click="showInvite = false"></div>
            <div x-transition.scale.duration.200ms style="position:relative;z-index:1;background:var(--card);border:1px solid var(--border);border-radius:12px;width:100%;max-width:440px;padding:20px">
                <h3 style="font-size:15px;font-weight:700;color:var(--text);margin:0 0 6px"><i class="fas fa-user-plus" style="margin-right:6px;color:var(--accent)"></i>{{ __('peer.invite_title') }}</h3>
                <p style="font-size:12px;color:var(--text-muted);margin:0 0 12px">{{ __('peer.invite_hint') }}</p>
                <div style="display:flex;gap:6px;align-items:center;background:var(--bg-2);border:1px solid var(--border);border-radius:8px;padding:8px 10px;margin-bottom:6px">
                    <code x-text="inviteLink" style="flex:1;font-family:'Courier New',monospace;font-size:11px;color:var(--accent);overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></code>
                    <button class="pr-btn pr-btn--accent" @click="copyInvite()"><i class="fas fa-copy"></i> {{ __('peer.copy') }}</button>
                </div>
                <div style="display:flex;gap:6px;align-items:center;background:var(--bg-2);border:1px solid var(--border);border-radius:8px;padding:8px 10px;margin-bottom:12px">
                    <code x-text="roomCode" style="flex:1;font-family:'Courier New',monospace;font-size:13px;font-weight:800;letter-spacing:2px;color:var(--text)"></code>
                    <button class="pr-btn" @click="copyCode()"><i class="fas fa-copy"></i> {{ __('peer.copy') }}</button>
                </div>
                <button class="pr-btn pr-btn--accent" style="width:100%;justify-content:center" @click="showInvite = false">{{ __('peer.done') }}</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const ICE_SERVERS = [{ urls: 'stun:stun.l.google.com:19302' }, { urls: 'stun:stun1.l.google.com:19302' }, { urls: 'stun:stun2.l.google.com:19302' }];
        function prRoom() {
            return {
                roomCode: '{{ $room->room_code }}', isHost:{{ $isHost ? 'true' : 'false' }}, currentUserId:{{ Auth::id() ?? 'null' }},
                peerName: '{{ addslashes($peerName ?? '') }}', peerConnected:{{ $peerConnected ? 'true' : 'false' }},
                localStream: null, remoteStream: null, pc: null, micOn: true, camOn: false, speakerOn: true,
                activeTab: 'tasks', showChat: false, localAudio: 0, audioCtx: null, localAnalyser: null, audioAnim: null,
                timer: 3600, messages: [], chatInput: '', lastMessageId: 0, signalingPollTimer: null, chatPreviewFile: null,
                tasks: @json($tasks), currentTaskId: null, showAddForm: false,
                newTask: { title: '', description: '', type: 'code', difficulty: 'medium', starter_code: '', language: 'python' },
                codeContent: @json($room->code_content ?? ''), codeLang: @json($room->code_language ?? 'python'), codeSaveTimer: null,
                toastMsg: '', toastIcon: 'fas fa-info-circle', toastColor: 'var(--accent)', toastShow: false, toastTimer: null, ended: false,
                showInvite: false, inviteLink: window.location.origin + '/peer/' + '{{ $room->room_code }}',
                codeRunning: false, codeOutput: '', codeError: '', codeStdin: '', codeLangTs: 0, codeInputTs: 0,
                boardColor: '#6366f1', boardWidth: 3, boardTool: 'pen', boardGrid: true, boardEraser: false, boardStrokes: @json(json_decode($room->board_content ?? '[]', true) ?? []), boardSynced: true, boardReady: false, boardSaveTimer: null, boardLastRemote: @json($room->board_content ?? ''),
                boardTextOpen: false, boardTextValue: '', boardTextPos: null, boardRev: @json((int)($room->board_rev ?? 0)), boardDrawing: false, boardDirty: false, boardUndo: [],
                boardColors: ['#6366f1', '#22c55e', '#ef4444', '#eab308', '#ffffff', '#0ea5e9'],

                get progressPercent() { if (!this.tasks.length) return 0; return Math.round((this.tasks.filter(t => t.status === 'done' || t.status === 'review').length / this.tasks.length) * 100) },
                get progressText() { const d = this.tasks.filter(t => t.status === 'done' || t.status === 'review').length; return d + ' / ' + this.tasks.length + ' {{ __('peer.tasks_completed') }}' },
                typeLabel(t) { return { code: '{{ __('peer.code') }}', theory: '{{ __('peer.theory') }}', system_design: '{{ __('peer.design') }}' }[t] || t },
                diffLabel(d) { return { easy: '{{ __('difficulty_easy') }}', medium: '{{ __('difficulty_medium') }}', hard: '{{ __('difficulty_hard') }}' }[d] || d },
                statusLabel(s) { return { active: '{{ __('peer.new_task') }}', in_progress: '{{ __('peer.in_progress') }}', done: '{{ __('peer.done') }}', skipped: '{{ __('peer.skipped') }}', review: '{{ __('peer.reviewed') }}' }[s] || s },

                toast(msg, icon, color) { this.toastMsg = msg; this.toastIcon = icon || 'fas fa-info-circle'; this.toastColor = color || 'var(--accent)'; this.toastShow = true; clearTimeout(this.toastTimer); this.toastTimer = setTimeout(() => { this.toastShow = false }, 3000) },
                fmt(s) { const m = Math.floor(s / 60); return String(m).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0') },
                copyCode() { navigator.clipboard.writeText(this.roomCode).then(() => { this.toast('{{ __('peer.code_copied') }}: ' + this.roomCode, 'fas fa-copy', 'var(--success)') }).catch(() => { prompt('{{ __("peer.copy_code_prompt") }}', this.roomCode) }) },

                async init() { this.showChat = window.innerWidth > 900; try { this.localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true }); this.$nextTick(() => { const v = this.$refs.localVideo; if (v) v.srcObject = this.localStream }); this.camOn = true; this.setupAudioAnalyser(); this.toast('{{ __('peer.camera_mic_connected') }}', 'fas fa-check-circle', 'var(--success)') } catch (e) { this.toast('{{ __('peer.camera_mic_error') }}', 'fas fa-exclamation-triangle', 'var(--danger)') } this.tasks.forEach(t => { t._solution = ''; t._score = t.score || ''; t._feedback = t.feedback || '' }); this.startTimer(); this.startSignaling() },
                setupAudioAnalyser() { if (!this.localStream) return; this.audioCtx = new (window.AudioContext || window.webkitAudioContext)(); const s = this.audioCtx.createMediaStreamSource(this.localStream); this.localAnalyser = this.audioCtx.createAnalyser(); this.localAnalyser.fftSize = 256; s.connect(this.localAnalyser); this.animateAudio() },
                animateAudio() { const loop = () => { if (this.ended) return; if (this.localAnalyser) { const d = new Uint8Array(this.localAnalyser.frequencyBinCount); this.localAnalyser.getByteFrequencyData(d); this.localAudio = Math.min(100, (d.reduce((a, b) => a + b, 0) / d.length) * 2) } this.audioAnim = requestAnimationFrame(loop) }; loop() },
                startTimer() { const started ={{ $room->started_at ? $room->started_at->timestamp : 'Math.floor(Date.now()/1000)' }}; const elapsed = Math.floor((Date.now() / 1000) - started); this.timer = Math.max(0, 3600 - elapsed); setInterval(() => { if (this.ended) return; if (this.timer > 0) this.timer--; if (this.timer <= 0) this.end() }, 1000) },

                async createPeerConnection() {
                    if (this.pc) return; this.pc = new RTCPeerConnection({ iceServers: ICE_SERVERS }); if (this.localStream) this.localStream.getTracks().forEach(t => this.pc.addTrack(t, this.localStream));
                    this.pc.ontrack = (e) => { this.remoteStream = e.streams[0]; this.peerConnected = true; this.$nextTick(() => { const v = this.$refs.remoteVideo; if (v) v.srcObject = this.remoteStream }); this.toast('{{ __('peer.peer_connected') }}', 'fas fa-user-check', 'var(--success)') };
                    this.pc.onicecandidate = (e) => { if (e.candidate) this.sendSignal('ice', e.candidate.toJSON()) };
                    this.pc.onconnectionstatechange = () => { if (this.pc.connectionState === 'connected') this.peerConnected = true; else if (this.pc.connectionState === 'disconnected' || this.pc.connectionState === 'failed') { this.peerConnected = false; this.toast('{{ __('peer.connection_lost') }}', 'fas fa-exclamation-triangle', 'var(--danger)') } };
                    if (this.isHost) { const o = await this.pc.createOffer(); await this.pc.setLocalDescription(o); this.sendSignal('sdp', this.pc.localDescription.toJSON()) }
                },
                async handleRemoteSdp(sdp) { if (!sdp || !this.pc) return; try { const d = new RTCSessionDescription(sdp); if (sdp.type === 'offer' && !this.isHost) { await this.pc.setRemoteDescription(d); const a = await this.pc.createAnswer(); await this.pc.setLocalDescription(a); this.sendSignal('sdp', this.pc.localDescription.toJSON()) } else if (sdp.type === 'answer' && this.isHost) { await this.pc.setRemoteDescription(d) } } catch (e) { } },
                async handleRemoteIce(ice) { if (!ice || !this.pc) return; try { await this.pc.addIceCandidate(new RTCIceCandidate(ice)) } catch (e) { } },

                async sendSignal(type, data) { try { await fetch('{{ route("peer.signal", $room->room_code) }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ type, data }) }) } catch (e) { console.warn('Signal send failed:', type, e) } },

                startSignaling() {
                    this._lastG = ''; this._lastH = ''; this._iceG = 0; this._iceH = 0; this._pollBusy = false; const poll = async () => {
                        if (this.ended || this._pollBusy) return; this._pollBusy = true; try {
                            const res = await fetch('{{ route("peer.signal", $room->room_code) }}?type=all', { headers: { 'Accept': 'application/json' } }); const data = await res.json();
                            if (data.status === 'ended') { this.toast('{{ __('peer.interview_ended') }}', 'fas fa-phone-slash', 'var(--danger)'); setTimeout(() => { window.location = '{{ route("peer.index") }}' }, 1500); return }
                            if (data.guest_connected) { if (!this.peerConnected) this.peerConnected = true; if (this.isHost && !this.pc) await this.createPeerConnection() }
                            const gSdp = data.guest_sdp ? JSON.stringify(data.guest_sdp) : ''; const hSdp = data.host_sdp ? JSON.stringify(data.host_sdp) : ''
                            if (this.isHost && data.guest_sdp && gSdp !== this._lastG) { if (!this.pc) await this.createPeerConnection(); await this.handleRemoteSdp(data.guest_sdp); this._lastG = gSdp }
                            if (!this.isHost && data.host_sdp && hSdp !== this._lastH) { if (!this.pc) await this.createPeerConnection(); await this.handleRemoteSdp(data.host_sdp); this._lastH = hSdp }
                            if (this.isHost && data.guest_ice && Array.isArray(data.guest_ice)) { const n = data.guest_ice.slice(this._iceG); for (const ice of n) await this.handleRemoteIce(ice); this._iceG = data.guest_ice.length }
                            if (!this.isHost && data.host_ice && Array.isArray(data.host_ice)) { const n = data.host_ice.slice(this._iceH); for (const ice of n) await this.handleRemoteIce(ice); this._iceH = data.host_ice.length }
                            if (data.tasks) { const snap = JSON.stringify(data.tasks.map(t => t.id + '-' + t.status + '-' + t.score + '-' + t.feedback)); const cur = JSON.stringify(this.tasks.map(t => t.id + '-' + t.status + '-' + t.score + '-' + t.feedback)); if (snap !== cur) { const prevSolutions = {}; this.tasks.forEach(t => { prevSolutions[t.id] = t._solution }); this.tasks = data.tasks; this.tasks.forEach(t => { t._solution = prevSolutions[t.id] || ''; t._score = t.score || ''; t._feedback = t.feedback || '' }) } }
                            if (data.code_content !== undefined && data.code_content !== null && data.code_content !== this.codeContent && (Date.now() - this.codeInputTs) > 2000) this.codeContent = data.code_content;
                            if (data.code_language !== undefined && data.code_language !== null && data.code_language !== this.codeLang && (Date.now() - this.codeLangTs) > 2500) { this.codeLang = data.code_language }
                            if (data.board_rev !== undefined && data.board_rev !== null && parseInt(data.board_rev) > this.boardRev && !this.boardDrawing && !this.boardDirty) { this.boardRev = parseInt(data.board_rev); this.boardLastRemote = data.board_content; try { this.boardStrokes = JSON.parse(data.board_content || '[]') } catch (e) { this.boardStrokes = [] } this.redrawBoard() }
                            if (data.messages && data.messages.length) { data.messages.forEach(m => { if (!this.messages.find(x => x.id === m.id)) { m.user_name = m.user ? m.user.name : 'Unknown'; this.messages.push(m) } }); this.$nextTick(() => { const b = this.$refs.chatBox; if (b) b.scrollTop = b.scrollHeight }) }
                        } catch (e) { } finally { this._pollBusy = false }
                    }; poll(); this.signalingPollTimer = setInterval(poll, 1200)
                },

                async sendChat() { if (!this.chatInput.trim() && !this.chatPreviewFile) return; const formData = new FormData(); formData.append('text', this.chatInput.trim() || ''); if (this.chatPreviewFile) formData.append('file', this.chatPreviewFile); try { const res = await fetch('{{ route("peer.message.send", $room->room_code) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: formData }); const data = await res.json(); if (data.ok) { data.message.user_name = '{{ __("peer.you") }}'; this.messages.push(data.message); this.chatInput = ''; this.chatPreviewFile = null; this.$nextTick(() => { const b = this.$refs.chatBox; if (b) b.scrollTop = b.scrollHeight }) } } catch (e) { } },

                handleChatFile(event) { const file = event.target.files[0]; if (!file) return; this.chatPreviewFile = file; event.target.value = ''; },

                fmtSize(bytes) { if (!bytes) return ''; if (bytes < 1024) return bytes + ' B'; if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB'; return (bytes / 1048576).toFixed(1) + ' MB'; },

                fixUrl(u) { if (!u) return u; const i = u.indexOf('/storage/'); if (i >= 0) return window.location.origin + u.substring(i); return u },

                openFile(u) { window.open(this.fixUrl(u), '_blank') },

                linkify(text) { if (!text) return ''; var urlRe = /(https?:\/\/[^\s<]+)/g; var escaped = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'); return escaped.replace(urlRe, '<a href="$1" target="_blank" style="text-decoration:underline;color:inherit">$1</a>'); },

                toggleMic() { if (!this.localStream) return; this.micOn = !this.micOn; this.localStream.getAudioTracks().forEach(t => { t.enabled = this.micOn }); this.toast(this.micOn ? '{{ __('peer.mic_on') }}' : '{{ __('peer.mic_off') }}', this.micOn ? 'fas fa-microphone' : 'fas fa-microphone-slash', this.micOn ? 'var(--success)' : 'var(--danger)') },
                toggleCam() { if (!this.localStream) return; this.camOn = !this.camOn; this.localStream.getVideoTracks().forEach(t => { t.enabled = this.camOn }); this.toast(this.camOn ? '{{ __('peer.cam_on') }}' : '{{ __('peer.cam_off') }}', this.camOn ? 'fas fa-video' : 'fas fa-video-slash', this.camOn ? 'var(--success)' : 'var(--danger)') },
                toggleSpeaker() { this.speakerOn = !this.speakerOn; const v = this.$refs.remoteVideo; if (v) v.muted = !this.speakerOn; this.toast(this.speakerOn ? '{{ __('peer.sound_on') }}' : '{{ __('peer.sound_off') }}', this.speakerOn ? 'fas fa-volume-up' : 'fas fa-volume-mute', this.speakerOn ? 'var(--success)' : 'var(--danger)') },

                loadTaskCode(task) { this.codeContent = task.starter_code || ''; this.codeLang = task.language || 'python'; this.codeLangTs = Date.now(); this.activeTab = 'code'; this.saveCode(true); this.toast('{{ __('peer.code_loaded') }}', 'fas fa-code', 'var(--accent)') },
                onCodeInput() { this.codeInputTs = Date.now(); clearTimeout(this.codeSaveTimer); this.codeSaveTimer = setTimeout(() => this.saveCode(false), 1000) },
                changeCodeLang() { this.codeLangTs = Date.now(); this.saveCode(true) },
                async saveCode(withLang) { try { const body = { code: this.codeContent || '' }; if (withLang) body.language = this.codeLang || 'python'; await fetch('{{ route("peer.code.update", $room->room_code) }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify(body) }) } catch (e) { console.warn('Code save failed:', e) } },

                copyInvite() { navigator.clipboard.writeText(this.inviteLink).then(() => { this.toast('{{ __('peer.invite_copied') }}', 'fas fa-link', 'var(--success)') }).catch(() => { prompt('{{ __("peer.copy_code_prompt") }}', this.inviteLink) }) },

                async runCode() { if (this.codeRunning) return; this.codeRunning = true; this.codeOutput = ''; this.codeError = ''; try { const res = await fetch('{{ route("peer.code.run", $room->room_code) }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ code: this.codeContent || '', language: this.codeLang || 'python', stdin: this.codeStdin || '' }) }); const data = await res.json(); if (data.ok) { this.codeOutput = (data.stdout || '') + (data.stdout ? '\n[' + data.status + ' · ' + (data.time || '?') + 's]' : '[' + data.status + ']'); this.codeError = data.stderr || ''; if (!data.stdout && !data.stderr) this.codeOutput = '[' + data.status + ']'; } else { this.codeError = JSON.stringify(data.error || data); } } catch (e) { this.codeError = '{{ __('peer.network_error') }}'; } this.codeRunning = false },

                initBoard() { const canvas = this.$refs.boardCanvas; if (!canvas || this.boardReady) { if (canvas && this.boardReady) this.redrawBoard(); return } this.boardReady = true; this.$nextTick(() => { this.fitBoard(); window.addEventListener('resize', () => this.fitBoard()); this.bindBoardEvents(); this.redrawBoard() }) },
                fitBoard() { const canvas = this.$refs.boardCanvas; if (!canvas) return; const r = canvas.getBoundingClientRect(); const dpr = window.devicePixelRatio || 1; canvas.width = Math.max(300, r.width * dpr); canvas.height = Math.max(300, (r.height || 400) * dpr); this.redrawBoard() },
                boardPos(e) { const canvas = this.$refs.boardCanvas; const r = canvas.getBoundingClientRect(); const t = e.touches ? e.touches[0] : e; return { x: (t.clientX - r.left) / r.width, y: (t.clientY - r.top) / r.height } },
                boardUid() { return 's' + Date.now().toString(36) + Math.floor(Math.random() * 1296).toString(36) },
                pushUndo(a) { this.boardUndo.push(a); if (this.boardUndo.length > 50) this.boardUndo.shift() },
                bindBoardEvents() { const canvas = this.$refs.boardCanvas; if (!canvas || canvas._bound) return; canvas._bound = true; let drawing = false; let cur = null; let p0 = null; const start = (e) => { if (this.boardTextOpen) return; e.preventDefault(); if (this.boardTool === 'text') { this.openBoardText(e); return } drawing = true; this.boardDrawing = true; p0 = this.boardPos(e); if (this.boardEraser) { const n = this.eraseAt(p0); if (n) this._eraseBuf = (this._eraseBuf || []).concat(n); return } const col = this.boardColor; const w = this.boardWidth; if (this.boardTool === 'pen') { cur = { id: this.boardUid(), kind: 'pen', c: col, w: w, u: this.currentUserId, pts: [p0] }; if (this.boardStrokes.length > 1000) this.boardStrokes.shift(); this.boardStrokes.push(cur); this.drawStroke(cur) } else { cur = { id: this.boardUid(), kind: this.boardTool, c: col, w: w, u: this.currentUserId, x1: p0.x, y1: p0.y, x2: p0.x, y2: p0.y } } }; const move = (e) => { if (!drawing) return; e.preventDefault(); const p = this.boardPos(e); if (this.boardEraser) { const n = this.eraseAt(p); if (n) this._eraseBuf = (this._eraseBuf || []).concat(n); return } if (!cur) return; if (cur.kind === 'pen') { cur.pts.push(p); this.drawStroke(cur) } else { cur.x2 = p.x; cur.y2 = p.y; this.redrawBoard(); this.drawStroke(cur) } }; const endEv = () => { if (!drawing) return; drawing = false; this.boardDrawing = false; if (this.boardEraser) { if (this._eraseBuf && this._eraseBuf.length) { this.pushUndo({ t: 'erase', items: this._eraseBuf }); this._eraseBuf = null } this.queueBoardSave(); return } if (cur && cur.kind !== 'pen') { if (Math.abs(cur.x2 - cur.x1) > 0.002 || Math.abs(cur.y2 - cur.y1) > 0.002) { if (this.boardStrokes.length > 1000) this.boardStrokes.shift(); this.boardStrokes.push(cur); this.pushUndo({ t: 'add', id: cur.id }) } this.redrawBoard() } else if (cur) { this.pushUndo({ t: 'add', id: cur.id }) } cur = null; p0 = null; this.queueBoardSave() }; canvas.addEventListener('mousedown', start); canvas.addEventListener('mousemove', move); window.addEventListener('mouseup', endEv); canvas.addEventListener('touchstart', start, { passive: false }); canvas.addEventListener('touchmove', move, { passive: false }); canvas.addEventListener('touchend', endEv) },
                eraseTol() { const canvas = this.$refs.boardCanvas; const r = canvas.getBoundingClientRect(); const px = (this.boardWidth * 3) / 2 + 8; return { x: px / Math.max(50, r.width), y: px / Math.max(50, r.height) } },
                eraseAt(p) { const t = this.eraseTol(); const removed = []; for (let i = this.boardStrokes.length - 1; i >= 0; i--) { if (this.hitStroke(this.boardStrokes[i], p, t.x, t.y)) removed.push({ s: this.boardStrokes[i], i: i }) } if (!removed.length) return null; removed.forEach(r => this.boardStrokes.splice(r.i, 1)); this.redrawBoard(); return removed },
                segDist(p, a, b) { const dx = b.x - a.x, dy = b.y - a.y; const l2 = dx * dx + dy * dy; let t = 0; if (l2 > 0) t = Math.max(0, Math.min(1, ((p.x - a.x) * dx + (p.y - a.y) * dy) / l2)); const cx = a.x + t * dx, cy = a.y + t * dy; return Math.hypot(p.x - cx, p.y - cy) },
                hitStroke(s, p, tx, ty) { const k = s.kind || 'pen'; if (k === 'pen' || k === 'erase') { return (s.pts || []).some(q => Math.abs(q.x - p.x) < tx && Math.abs(q.y - p.y) < ty) } if (k === 'line' || k === 'arrow') { return this.segDist(p, { x: s.x1, y: s.y1 }, { x: s.x2, y: s.y2 }) < Math.max(tx, ty) } if (k === 'rect') { const x0 = Math.min(s.x1, s.x2) - tx, x1 = Math.max(s.x1, s.x2) + tx, y0 = Math.min(s.y1, s.y2) - ty, y1 = Math.max(s.y1, s.y2) + ty; return p.x >= x0 && p.x <= x1 && p.y >= y0 && p.y <= y1 } if (k === 'circle') { const cx = (s.x1 + s.x2) / 2, cy = (s.y1 + s.y2) / 2, rx = Math.abs(s.x2 - s.x1) / 2 + tx, ry = Math.abs(s.y2 - s.y1) / 2 + ty; if (rx <= 0 || ry <= 0) return false; const v = ((p.x - cx) * (p.x - cx)) / (rx * rx) + ((p.y - cy) * (p.y - cy)) / (ry * ry); return v <= 1 } if (k === 'text') { const size = s.size || 0.035; const w = (s.text || '').length * size * 0.55 + tx; return p.x >= s.x - tx && p.x <= s.x + w && p.y >= s.y - size - ty && p.y <= s.y + ty } return false },
                drawShape(s, ctx, W, H) { const x1 = s.x1 * W, y1 = s.y1 * H, x2 = s.x2 * W, y2 = s.y2 * H; ctx.beginPath(); if (s.kind === 'line' || s.kind === 'arrow') { ctx.moveTo(x1, y1); ctx.lineTo(x2, y2); ctx.stroke(); if (s.kind === 'arrow') { const ang = Math.atan2(y2 - y1, x2 - x1); const hl = Math.max(8, (s.w || 3) * 3 * (window.devicePixelRatio || 1)); ctx.beginPath(); ctx.moveTo(x2, y2); ctx.lineTo(x2 - hl * Math.cos(ang - 0.4), y2 - hl * Math.sin(ang - 0.4)); ctx.moveTo(x2, y2); ctx.lineTo(x2 - hl * Math.cos(ang + 0.4), y2 - hl * Math.sin(ang + 0.4)); ctx.stroke() } } else if (s.kind === 'rect') { ctx.strokeRect(Math.min(x1, x2), Math.min(y1, y2), Math.abs(x2 - x1), Math.abs(y2 - y1)) } else if (s.kind === 'circle') { ctx.ellipse((x1 + x2) / 2, (y1 + y2) / 2, Math.abs(x2 - x1) / 2, Math.abs(y2 - y1) / 2, 0, 0, Math.PI * 2); ctx.stroke() } },
                drawStroke(s) { const canvas = this.$refs.boardCanvas; if (!canvas) return; const ctx = canvas.getContext('2d'); const W = canvas.width, H = canvas.height; ctx.save(); ctx.lineCap = 'round'; ctx.lineJoin = 'round'; if (s.kind === 'erase') { ctx.globalCompositeOperation = 'destination-out'; ctx.strokeStyle = 'rgba(0,0,0,1)' } else { ctx.strokeStyle = s.c || '#6366f1'; ctx.fillStyle = s.c || '#6366f1' } ctx.lineWidth = (s.w || 3) * (window.devicePixelRatio || 1); if (!s.kind || s.kind === 'pen' || s.kind === 'erase') { ctx.beginPath(); (s.pts || []).forEach((p, i) => { const x = p.x * W, y = p.y * H; if (i === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y) }); ctx.stroke() } else if (s.kind === 'text') { ctx.font = Math.max(8, (s.size || 0.035) * H) + 'px Inter, sans-serif'; ctx.fillText(s.text || '', (s.x || 0) * W, (s.y || 0) * H) } else { this.drawShape(s, ctx, W, H) } ctx.restore() },
                redrawBoard() { const canvas = this.$refs.boardCanvas; if (!canvas || !this.boardReady) return; const ctx = canvas.getContext('2d'); ctx.save(); ctx.globalCompositeOperation = 'source-over'; ctx.clearRect(0, 0, canvas.width, canvas.height); if (this.boardGrid) { const dpr = window.devicePixelRatio || 1; const step = 32 * dpr; ctx.strokeStyle = 'rgba(148,163,184,.14)'; ctx.lineWidth = 1; ctx.beginPath(); for (let x = step; x < canvas.width; x += step) { ctx.moveTo(x, 0); ctx.lineTo(x, canvas.height) } for (let y = step; y < canvas.height; y += step) { ctx.moveTo(0, y); ctx.lineTo(canvas.width, y) } ctx.stroke() } ctx.restore(); (this.boardStrokes || []).forEach(s => this.drawStroke(s)) },
                openBoardText(e) { const canvas = this.$refs.boardCanvas; const r = canvas.getBoundingClientRect(); const p = this.boardPos(e); this.boardTextPos = p; this.boardTextValue = ''; this.boardTextOpen = true; this.$nextTick(() => { const inp = this.$refs.boardTextInput; inp.style.left = (p.x * r.width) + 'px'; inp.style.top = (p.y * r.height) + 'px'; inp.style.color = this.boardColor; inp.focus() }) },
                commitBoardText() { const v = (this.boardTextValue || '').trim(); this.boardTextOpen = false; if (!v || !this.boardTextPos) return; const canvas = this.$refs.boardCanvas; const r = canvas.getBoundingClientRect(); if (this.boardStrokes.length > 1000) this.boardStrokes.shift(); const st = { id: this.boardUid(), kind: 'text', c: this.boardColor, size: 15 / Math.max(200, r.height), x: this.boardTextPos.x, y: this.boardTextPos.y, text: v.slice(0, 300), u: this.currentUserId }; this.boardStrokes.push(st); this.pushUndo({ t: 'add', id: st.id }); this.boardTextPos = null; this.boardTextValue = ''; this.redrawBoard(); this.queueBoardSave() },
                undoBoard() { const a = this.boardUndo.pop(); if (!a) return; if (a.t === 'add') { const i = this.boardStrokes.findIndex(s => s.id === a.id); if (i >= 0) this.boardStrokes.splice(i, 1) } else if (a.t === 'erase') { const items = (a.items || []).slice().sort((x, y) => x.i - y.i); items.forEach(r => this.boardStrokes.splice(Math.min(r.i, this.boardStrokes.length), 0, r.s)) } else if (a.t === 'clear') { this.boardStrokes = (a.strokes || []).slice() } this.redrawBoard(); this.queueBoardSave() },
                exportBoard() { const canvas = this.$refs.boardCanvas; if (!canvas) return; const off = document.createElement('canvas'); off.width = canvas.width; off.height = canvas.height; const ctx = off.getContext('2d'); ctx.fillStyle = '#0f172a'; ctx.fillRect(0, 0, off.width, off.height); ctx.drawImage(canvas, 0, 0); const a = document.createElement('a'); a.download = 'board-' + this.roomCode + '.png'; a.href = off.toDataURL('image/png'); a.click(); this.toast('PNG', 'fas fa-download', 'var(--success)') },
                queueBoardSave() { this.boardDirty = true; this.boardSynced = false; clearTimeout(this.boardSaveTimer); this.boardSaveTimer = setTimeout(() => this.saveBoard(), 800) },
                async saveBoard() { const snapshot = JSON.stringify(this.boardStrokes || []); const rev = this.boardRev + 1; try { const res = await fetch('{{ route("peer.signal", $room->room_code) }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ type: 'board', data: snapshot, rev: rev }) }); const out = await res.json(); if (out && out.ok) { this.boardRev = out.rev || rev; this.boardLastRemote = snapshot; this.boardDirty = false; this.boardSynced = true } else if (out && out.rev !== undefined) { this.boardRev = parseInt(out.rev) || 0; this.boardLastRemote = out.board_content; try { this.boardStrokes = JSON.parse(out.board_content || '[]') } catch (e) { this.boardStrokes = [] } this.boardDirty = false; this.boardSynced = true; this.redrawBoard() } } catch (e) { } },
                clearBoard() { if (!this.boardStrokes.length) return; this.pushUndo({ t: 'clear', strokes: this.boardStrokes.slice() }); this.boardStrokes = []; this.redrawBoard(); this.queueBoardSave() },

                async addTask() { if (!this.newTask.title.trim()) return; try { const res = await fetch('{{ route("peer.task.add", $room->room_code) }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify(this.newTask) }); const data = await res.json(); if (data.ok) { const t = data.task; t._solution = ''; t._score = ''; t._feedback = ''; this.tasks.push(t); this.newTask = { title: '', description: '', type: 'code', difficulty: 'medium', starter_code: '', language: 'python' }; this.showAddForm = false; this.toast('{{ __('peer.task_added') }}', 'fas fa-check', 'var(--success)') } } catch (e) { this.toast('{{ __('peer.task_add_error') }}', 'fas fa-exclamation-triangle', 'var(--danger)') } },

                async startTask(task) { try { const res = await fetch('{{ url("peer/" . $room->room_code . "/task") }}/' + task.id + '/start', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }); const data = await res.json(); if (data.ok) { task.status = 'in_progress'; this.toast('{{ __('peer.task_started') }}', 'fas fa-play', 'var(--accent)') } else { this.toast(data.error || '{{ __('peer_error') }}', 'fas fa-exclamation-triangle', 'var(--danger)') } } catch (e) { this.toast('{{ __('peer.network_error') }}', 'fas fa-exclamation-triangle', 'var(--danger)') } },

                async submitTask(task) { const solution = task._solution || ''; if (!solution.trim()) { this.toast('{{ __('peer.enter_answer') }}', 'fas fa-exclamation-triangle', 'var(--warning)'); return } try { const res = await fetch('{{ url("peer/" . $room->room_code . "/task") }}/' + task.id + '/submit', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ solution }) }); const data = await res.json(); if (data.ok) { task.status = 'done'; task.solution = solution; task._solution = ''; this.toast('{{ __('peer.solution_submitted') }}', 'fas fa-check-circle', 'var(--success)') } else { this.toast(data.error || '{{ __('peer_error') }}', 'fas fa-exclamation-triangle', 'var(--danger)') } } catch (e) { this.toast('{{ __('peer.network_error') }}', 'fas fa-exclamation-triangle', 'var(--danger)') } },

                async reviewTask(task) { const score = parseInt(task._score); if (isNaN(score) || score < 0 || score > 10) { this.toast('{{ __('peer.score_0_to_10') }}', 'fas fa-exclamation-triangle', 'var(--warning)'); return } try { const res = await fetch('{{ url("peer/" . $room->room_code . "/task") }}/' + task.id + '/review', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ score, feedback: task._feedback || '' }) }); const data = await res.json(); if (data.ok) { task.status = 'review'; task.score = score; task.feedback = task._feedback || ''; this.toast('{{ __('peer.task_reviewed') }}: ' + score + '/10', 'fas fa-star', 'var(--success)') } else { this.toast(data.error || '{{ __('peer_error') }}', 'fas fa-exclamation-triangle', 'var(--danger)') } } catch (e) { this.toast('{{ __('peer.network_error') }}', 'fas fa-exclamation-triangle', 'var(--danger)') } },

                async skipTask(task) { try { const res = await fetch('{{ url("peer/" . $room->room_code . "/task") }}/' + task.id, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }, body: JSON.stringify({ status: 'skipped' }) }); const data = await res.json(); if (data.ok) { task.status = 'skipped'; this.toast('{{ __('peer.task_skipped') }}', 'fas fa-forward', 'var(--text-muted)') } else { this.toast(data.error || '{{ __('peer_error') }}', 'fas fa-exclamation-triangle', 'var(--danger)') } } catch (e) { this.toast('{{ __('peer.network_error') }}', 'fas fa-exclamation-triangle', 'var(--danger)') } },

                async removeTask(taskId) { try { const res = await fetch('{{ url("peer/" . $room->room_code . "/task") }}/' + taskId, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }); const data = await res.json(); if (data.ok) { this.tasks = this.tasks.filter(t => t.id !== taskId); this.toast('{{ __('peer.task_deleted') }}', 'fas fa-trash', 'var(--danger)') } else { this.toast(data.error || '{{ __('peer_error') }}', 'fas fa-exclamation-triangle', 'var(--danger)') } } catch (e) { this.toast('{{ __('peer.network_error') }}', 'fas fa-exclamation-triangle', 'var(--danger)') } },

                end() { if (this.ended) return; this.ended = true; this.cleanup(); fetch('{{ route("peer.leave", $room->room_code) }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }).then(() => { window.location = '{{ route("peer.index") }}' }).catch(() => { window.location = '{{ route("peer.index") }}' }) },
                cleanup() { if (this.pc) { this.pc.close(); this.pc = null } if (this.localStream) { this.localStream.getTracks().forEach(t => t.stop()); this.localStream = null } if (this.audioCtx) { this.audioCtx.close(); this.audioCtx = null } if (this.audioAnim) cancelAnimationFrame(this.audioAnim); clearInterval(this.signalingPollTimer) }
            }
        }
    </script>
@endsection