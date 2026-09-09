@php
    $isDone = in_array($node['id'], $completedNodeIds);
    $hasChildren = count($node['children']) > 0;
    $hasLessons = count($node['lessons']) > 0;
    $hasMaterials = !empty($node['materials']) && is_array($node['materials']);
    $hasContent = $hasLessons || $hasMaterials;
    $topicKey = strtolower(preg_replace('/[^a-z]+/i', '', $node['topic'] ?? ''));
    $deps = $node['deps'] ?? [];
    if (is_string($deps)) { $decoded = json_decode($deps, true); $deps = is_array($decoded) ? $decoded : []; }
    if (!is_array($deps)) $deps = [];
    $depsMet = empty($deps) || collect($deps)->every(fn($d) => in_array((int)$d, $completedNodeIds));
    $isLocked = !$isDone && !$depsMet;
    $isExam = !empty($node['is_exam']);
@endphp

<div class="rm-tree-node" data-node-id="{{ $node['id'] }}" style="--depth:{{ $_depth ?? 0 }}">
    <div class="rm-tree-card {{ $isDone ? 'rm-done' : '' }} {{ $isLocked ? 'rm-locked' : '' }} js-rm-card"
         data-node-id="{{ $node['id'] }}"
         data-title="{{ e(__($node['title'])) }}"
         data-topic="{{ e(__($node['topic'] ?? '')) }}"
         data-materials="{{ e(json_encode($node['materials'] ?? [], JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_TAG)) }}"
         data-locked="{{ $isLocked ? '1' : '0' }}"
         data-exam="{{ $isExam ? '1' : '0' }}">
        <div class="rm-card-category" data-topic="{{ $topicKey }}">{{ __( strtoupper($node['topic'] ?? 'TOPIC') ) }}</div>
        <div class="rm-card-title">{{ __($node['title']) }}</div>
        <div class="rm-card-footer">
            @if($isLocked)
                <span class="rm-card-badge" style="background:rgba(100,116,139,.15);color:#64748b;border:1px solid rgba(100,116,139,.2)"><i class="fas fa-lock" style="font-size:8px"></i> {{ __('LOCKED') }}</span>
            @elseif($isDone)
                <span class="rm-card-badge rm-card-badge--done"><i class="fas fa-check" style="font-size:8px"></i> {{ __('DONE') }}</span>
            @elseif($isExam)
                <span class="rm-card-badge" style="background:rgba(168,85,247,.15);color:#a855f7;border:1px solid rgba(168,85,247,.2)">EXAM</span>
            @elseif($node['course_id'])
                <span class="rm-card-badge rm-card-badge--course"><i class="fas fa-graduation-cap" style="font-size:8px"></i> {{ __('COURSE') }}</span>
            @endif
            @if($hasContent)
                <span class="rm-card-count">{{ count($node['lessons']) + ($hasMaterials ? 1 : 0) }}</span>
            @endif
        </div>
    </div>

    @if($hasChildren)
        <div class="rm-tree-children">
            @foreach($node['children'] as $child)
                @include('roadmaps._tree_node', ['node' => $child, 'completedNodeIds' => $completedNodeIds, '_depth' => ($_depth ?? 0) + 1])
            @endforeach
        </div>
    @endif
</div>
