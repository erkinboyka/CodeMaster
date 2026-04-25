<?php if (!defined('APP_INIT')) die('Direct access not permitted'); ?>
<?php
$vizLang = currentLang();
$VIZ_I18N = [
    'ru' => [
    'hero_title' => 'Визуализация алгоритмов:',
    'hero_title_emphasis' => 'видеть логику кода',
    'hero_desc' => 'Интерактивные визуализации 20+ алгоритмов и структур данных в стиле VisuAlgo.net. Шаг за шагом наблюдайте за работой сортировок, графов, деревьев и других фундаментальных алгоритмов.',
    'start_demo' => 'Начать с демо',
    'all_algorithms' => 'Все алгоритмы',
    'status_ready' => 'Готов к запуску',
    'status_running' => 'Выполнение: {name}',
    'status_pause' => 'Пауза',
    'status_done' => 'Алгоритм завершен',
    'status_completed' => 'Завершено!',
    'status_new_data' => 'Новый набор данных готов',
    'live_demo' => 'Live Demo',
    'control_start' => 'Старт',
    'control_pause' => 'Пауза',
    'control_step' => 'Шаг',
    'control_reset' => 'Сброс',
    'control_shuffle' => 'Перемешать',
    'control_speed' => 'Скорость',
    'legend_active' => 'Активный',
    'legend_compare' => 'Сравнение',
    'legend_sorted' => 'Отсортирован',
    'stat_comparisons' => 'Сравнения',
    'stat_swaps' => 'Обмены',
    'stat_complexity' => 'Сложность',
    'algorithms_label' => 'Алгоритмы',
    'algorithms_title' => 'Каталог визуализаций',
    'algorithms_desc' => 'Выберите алгоритм для интерактивной демонстрации. Все визуализации включают пошаговое выполнение, псевдокод с подсветкой и анализ сложности.',
    'tab_sorting' => 'Сортировки',
    'tab_graphs' => 'Графы',
    'tab_trees' => 'Деревья',
    'tab_structures' => 'Структуры',
    'bubble_desc' => 'Простая сортировка обменами',
    'tag_stable' => 'Стабильная',
    'tag_adaptive' => 'Адаптивная',
    'button_launch' => 'Запустить',
    'algo_bubble' => 'Пузырьковая сортировка',
    'algo_quick' => 'Быстрая сортировка',
    'algo_merge' => 'Сортировка слиянием',
    'algo_bfs' => 'Поиск в ширину',
    'algo_dfs' => 'Поиск в глубину',
    'algo_dijkstra' => 'Алгоритм Дейкстры',
    'algo_bst' => 'Дерево поиска',
    'algo_avl' => 'AVL-дерево',
    'algo_stack' => 'Стек (LIFO)',
    'algo_queue' => 'Очередь (FIFO)',
    'algo_hash' => 'Хеш-таблица',
    'quick_desc' => 'Быстрая сортировка (разделяй и властвуй)',
    'tag_unstable' => 'Нестабильная',
    'tag_inplace' => 'На месте',
    'merge_desc' => 'Сортировка слиянием',
    'tag_memory' => 'Доп. память',
    'bfs_desc' => 'Поиск в ширину',
    'tag_shortest' => 'Кратчайший путь',
    'tag_queue' => 'Очередь',
    'dfs_desc' => 'Поиск в глубину',
    'tag_recursion' => 'Рекурсия',
    'tag_stack' => 'Стек',
    'dijkstra_desc' => 'Кратчайший путь',
    'tag_priority_queue' => 'Приоритетная очередь',
    'tag_nonnegative' => 'Неотрицательные веса',
    'bst_desc' => 'Визуализация дерева поиска: вставка, поиск, обходы.',
    'avl_desc' => 'Балансировка дерева и ротации в реальном времени.',
    'stack_desc' => 'Push/Pop по шагам с подсветкой вершины стека.',
    'queue_desc' => 'Enqueue/Dequeue с визуальным хвостом и головой.',
    'hash_desc' => 'Коллизии, buckets, вставка и поиск ключей на примерах.',
    'learning_label' => 'Обучение',
    'learning_title' => 'Почему визуализация работает',
    'learning_desc' => 'Научные исследования показывают, что визуальное представление алгоритмов повышает понимание на 40% по сравнению с текстовым описанием.',
    'learning_card1_title' => 'Видите состояние',
    'learning_card1_body' => 'Наблюдайте за изменением структуры данных на каждом шаге алгоритма в реальном времени.',
    'learning_card2_title' => 'Связь кода и поведения',
    'learning_card2_body' => 'Псевдокод с подсветкой текущей строки помогает соотнести абстрактную логику с конкретными действиями.',
    'learning_card3_title' => 'Анализ производительности',
    'learning_card3_body' => 'Статистика операций и визуализация сложности помогают понять эффективность алгоритма.',
],
    'en' => [
        'hero_title' => 'Algorithm visualizations:',
        'hero_title_emphasis' => 'see the code logic',
        'hero_desc' => 'Interactive visualizations of 20+ algorithms and data structures in the style of VisuAlgo.net. Step by step, watch how sorting, graphs, trees, and other core algorithms work.',
        'start_demo' => 'Start demo',
        'all_algorithms' => 'All algorithms',
        'status_ready' => 'Ready to run',
        'status_running' => 'Running: {name}',
        'status_pause' => 'Paused',
        'status_done' => 'Algorithm finished',
        'status_completed' => 'Done!',
        'status_new_data' => 'New dataset is ready',
        'live_demo' => 'Live Demo',
        'control_start' => 'Start',
        'control_pause' => 'Pause',
        'control_step' => 'Step',
        'control_reset' => 'Reset',
        'control_shuffle' => 'Shuffle',
        'control_speed' => 'Speed',
        'legend_active' => 'Active',
        'legend_compare' => 'Comparing',
        'legend_sorted' => 'Sorted',
        'stat_comparisons' => 'Comparisons',
        'stat_swaps' => 'Swaps',
        'stat_complexity' => 'Complexity',
        'algorithms_label' => 'Algorithms',
        'algorithms_title' => 'Visualization catalog',
        'algorithms_desc' => 'Choose an algorithm for an interactive demo. All visualizations include step-by-step execution, highlighted pseudocode, and complexity analysis.',
        'tab_sorting' => 'Sorting',
        'tab_graphs' => 'Graphs',
        'tab_trees' => 'Trees',
        'tab_structures' => 'Structures',
        'bubble_desc' => 'Simple swap-based sorting',
        'tag_stable' => 'Stable',
        'tag_adaptive' => 'Adaptive',
        'button_launch' => 'Launch',
        'algo_bubble' => 'Bubble sort',
        'algo_quick' => 'Quick sort',
        'algo_merge' => 'Merge sort',
        'algo_bfs' => 'Breadth-first search',
        'algo_dfs' => 'Depth-first search',
        'algo_dijkstra' => 'Dijkstra algorithm',
        'algo_bst' => 'Binary search tree',
        'algo_avl' => 'AVL tree',
        'algo_stack' => 'Stack (LIFO)',
        'algo_queue' => 'Queue (FIFO)',
        'algo_hash' => 'Hash table',
        'quick_desc' => 'Quick sort (divide and conquer)',
        'tag_unstable' => 'Unstable',
        'tag_inplace' => 'In-place',
        'merge_desc' => 'Merge sort',
        'tag_memory' => 'Extra memory',
        'bfs_desc' => 'Breadth-first search',
        'tag_shortest' => 'Shortest path',
        'tag_queue' => 'Queue',
        'dfs_desc' => 'Depth-first search',
        'tag_recursion' => 'Recursion',
        'tag_stack' => 'Stack',
        'dijkstra_desc' => 'Shortest path',
        'tag_priority_queue' => 'Priority queue',
        'tag_nonnegative' => 'Non-negative weights',
        'bst_desc' => 'Search tree visualization: insert, search, traversals.',
        'avl_desc' => 'Tree balancing and rotations in real time.',
        'stack_desc' => 'Step-by-step push/pop with top highlight.',
        'queue_desc' => 'Enqueue/dequeue with visible head and tail.',
        'hash_desc' => 'Collisions, buckets, insert/search examples.',
        'learning_label' => 'Learning',
        'learning_title' => 'Why visualization works',
        'learning_desc' => 'Studies show that visual representations increase understanding by up to 40% compared to text-only explanations.',
        'learning_card1_title' => 'See the state',
        'learning_card1_body' => 'Watch the data structure change on every step in real time.',
        'learning_card2_title' => 'Code meets behavior',
        'learning_card2_body' => 'Highlighted pseudocode links abstract logic to concrete actions.',
        'learning_card3_title' => 'Performance insights',
        'learning_card3_body' => 'Operation stats and complexity visuals help compare efficiency.',
    ],
'tg' => [
    'hero_title' => 'Визуализатсияи алгоритмҳо:',
    'hero_title_emphasis' => 'мантиқи кодро бинед',
    'hero_desc' => 'Визуализатсияҳои интерактивии зиёда аз 20 алгоритм ва сохторҳои маълумот дар услуби VisuAlgo.net. Қадам ба қадам кори сортировкаҳо, графҳо, дарахтҳо ва дигар алгоритмҳои асосиро бинед.',
    'start_demo' => 'Оғози демо',
    'all_algorithms' => 'Ҳама алгоритмҳо',
    'status_ready' => 'Омода барои оғоз',
    'status_running' => 'Иҷро: {name}',
    'status_pause' => 'Таваққуф',
    'status_done' => 'Алгоритм анҷом ёфт',
    'status_completed' => 'Анҷом ёфт!',
    'status_new_data' => 'Маълумоти нав омода шуд',
    'live_demo' => 'Намоиши зинда',
    'control_start' => 'Оғоз',
    'control_pause' => 'Таваққуф',
    'control_step' => 'Қадам',
    'control_reset' => 'Аз нав',
    'control_shuffle' => 'Омехта',
    'control_speed' => 'Суръат',
    'legend_active' => 'Фаъол',
    'legend_compare' => 'Муқоиса',
    'legend_sorted' => 'Сортшуда',
    'stat_comparisons' => 'Муқоисаҳо',
    'stat_swaps' => 'Ивазҳо',
    'stat_complexity' => 'Мураккабӣ',
    'algorithms_label' => 'Алгоритмҳо',
    'algorithms_title' => 'Феҳристи визуализатсияҳо',
    'algorithms_desc' => 'Алгоритмро барои намоиши интерактивӣ интихоб кунед. Ҳама визуализатсияҳо иҷрои қадам-ба-қадам, псевдокод бо равшанкунӣ ва таҳлили мураккабиро дар бар мегиранд.',
    'tab_sorting' => 'Сортировкаҳо',
    'tab_graphs' => 'Графҳо',
    'tab_trees' => 'Дарахтҳо',
    'tab_structures' => 'Сохторҳо',
    'bubble_desc' => 'Сортировкаи оддӣ бо ивазкунӣ',
    'tag_stable' => 'Стабил',
    'tag_adaptive' => 'Адаптивӣ',
    'button_launch' => 'Оғоз кардан',
    'algo_bubble' => 'Сортировкаи пуфакӣ',
    'algo_quick' => 'Сортировкаи зуд',
    'algo_merge' => 'Сортировка бо якҷоякунӣ',
    'algo_bfs' => 'Ҷустуҷӯ дар паҳноӣ',
    'algo_dfs' => 'Ҷустуҷӯ дар чуқурӣ',
    'algo_dijkstra' => 'Алгоритми Дейкстра',
    'algo_bst' => 'Дарахти ҷустуҷӯ',
    'algo_avl' => 'AVL-дарахт',
    'algo_stack' => 'Стек (LIFO)',
    'algo_queue' => 'Навбат (FIFO)',
    'algo_hash' => 'Ҳеш-ҷадвал',
    'quick_desc' => 'Сортировкаи зуд (тақсим кун ва ҳукмронӣ кун)',
    'tag_unstable' => 'Ностабил',
    'tag_inplace' => 'Дар ҷой',
    'merge_desc' => 'Сортировка бо якҷоякунӣ',
    'tag_memory' => 'Хотираи иловагӣ',
    'bfs_desc' => 'Ҷустуҷӯ дар паҳноӣ',
    'tag_shortest' => 'Кӯтоҳтарин роҳ',
    'tag_queue' => 'Навбат',
    'dfs_desc' => 'Ҷустуҷӯ дар чуқурӣ',
    'tag_recursion' => 'Рекурсия',
    'tag_stack' => 'Стек',
    'dijkstra_desc' => 'Кӯтоҳтарин роҳ',
    'tag_priority_queue' => 'Навбати афзалиятнок',
    'tag_nonnegative' => 'Вазнҳои ғайриманфӣ',
    'bst_desc' => 'Визуализатсияи дарахти ҷустуҷӯ: воридкунӣ, ҷустуҷӯ ва гузаришҳо.',
    'avl_desc' => 'Тавозуни дарахт ва ротацияҳо дар вақти воқеӣ.',
    'stack_desc' => 'Push/Pop қадам-ба-қадам бо нишон додани болои стек.',
    'queue_desc' => 'Enqueue/Dequeue бо нишон додани сар ва дум.',
    'hash_desc' => 'Коллизияҳо, buckets, воридкунӣ ва ҷустуҷӯи калидҳо.',
    'learning_label' => 'Омӯзиш',
    'learning_title' => 'Чаро визуализатсия кор мекунад',
    'learning_desc' => 'Тадқиқотҳо нишон медиҳанд, ки намоиши визуалӣ фаҳмишро то 40% беҳтар мекунад нисбат ба матни оддӣ.',
    'learning_card1_title' => 'Ҳолатро бинед',
    'learning_card1_body' => 'Тағйирёбии сохтори маълумотро дар ҳар қадам дар вақти воқеӣ бинед.',
    'learning_card2_title' => 'Пайванди код ва рафтор',
    'learning_card2_body' => 'Псевдокод бо равшанкунӣ мантиқи абстрактиро бо амалҳо пайваст мекунад.',
    'learning_card3_title' => 'Таҳлили самаранокӣ',
    'learning_card3_body' => 'Омор ва визуализатсияи мураккабӣ самаранокиро нишон медиҳанд.',
],
];
$viz = $VIZ_I18N[$vizLang] ?? $VIZ_I18N['ru'];
if (function_exists('normalizeMojibakeText')) {
    foreach ($viz as $vizKey => $vizValue) {
        if (is_string($vizValue)) {
            $viz[$vizKey] = normalizeMojibakeText($vizValue);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(currentLang()) ?>">

<head>
    <?php include __DIR__ . '/../includes/head_meta.php'; ?>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= t('visualizations_page_title') ?> - CodeMaster</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Manrope', 'sans-serif']
                    },
                    colors: {
                        tf: {
                            ink: '#10233E',
                            sky: '#E9F3FF',
                            blue: '#2C7BFF',
                            aqua: '#19C2C9',
                            mint: '#A7F3D0',
                            gold: '#FFD166',
                            coral: '#FF8A5B',
                            violet: '#7C5CFF',
                            emerald: '#10B981'
                        }
                    },
                    boxShadow: {
                        panel: '0 24px 60px rgba(16, 35, 62, 0.12)',
                        card: '0 12px 32px rgba(16, 35, 62, 0.09)'
                    },
                    animation: {
                        'float': 'float 3s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite'
                    }
                }
            }
        };
    </script>
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        body {
            font-family: 'Manrope', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(44, 123, 255, 0.08), transparent 35%),
                radial-gradient(circle at bottom right, rgba(25, 194, 201, 0.1), transparent 30%),
                #f0f5ff;
            color: #10233E;
            overflow-x: hidden;
        }
        .viz-shell {
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(148, 172, 201, 0.45);
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            border-radius: 32px;
        }
        .viz-shell::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(170, 190, 215, 0.15) 1px, transparent 1px),
                linear-gradient(90deg, rgba(170, 190, 215, 0.15) 1px, transparent 1px);
            background-size: 32px 32px;
            pointer-events: none;
            z-index: 0;
        }
        .viz-stage {
            position: relative;
            border-radius: 28px;
            border: 1px solid rgba(167, 183, 211, 0.5);
            background:
                linear-gradient(180deg, rgba(246, 250, 255, 0.96), rgba(255, 255, 255, 0.98));
            box-shadow: 0 26px 56px rgba(16, 35, 62, 0.08);
            overflow: hidden;
        }
        .viz-grid {
            --viz-motion-ms: 900ms;
            display: flex;
            flex-direction: row;
            align-items: flex-end;
            justify-content: center;
            gap: 12px;
            min-height: 320px;
            padding-bottom: 42px;
            position: relative;
            background-image:
                linear-gradient(rgba(191, 211, 233, 0.26) 1px, transparent 1px),
                linear-gradient(90deg, rgba(191, 211, 233, 0.26) 1px, transparent 1px);
            background-size: 26px 26px;
        }
        .viz-grid::after {
            content: "";
            position: absolute;
            left: 18px;
            right: 18px;
            bottom: 24px;
            height: 2px;
            border-radius: 999px;
            background: linear-gradient(90deg, rgba(148, 172, 201, 0.2), rgba(71, 85, 105, 0.35), rgba(148, 172, 201, 0.2));
            pointer-events: none;
        }
        .viz-grid.viz-grid-vertical {
            flex-direction: column-reverse;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;
        }
        .viz-bar {
            position: relative;
            width: var(--bar-width, 48px);
            min-width: 24px;
            max-width: 132px;
            transform-origin: bottom center;
            border-radius: 20px 20px 10px 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            padding-bottom: 12px;
            color: rgba(16, 35, 62, 0.85);
            font-size: 13px;
            font-weight: 800;
            will-change: height, width, transform, box-shadow;
            box-shadow: 
                inset 0 2px 8px rgba(255, 255, 255, 0.6),
                inset 0 -8px 18px rgba(16, 35, 62, 0.08),
                0 10px 24px rgba(16, 35, 62, 0.12);
            transition:
                height var(--viz-motion-ms) cubic-bezier(0.22, 1, 0.36, 1),
                width var(--viz-motion-ms) cubic-bezier(0.22, 1, 0.36, 1),
                transform calc(var(--viz-motion-ms) * 0.9) cubic-bezier(0.22, 1, 0.36, 1),
                box-shadow calc(var(--viz-motion-ms) * 0.9) ease,
                opacity calc(var(--viz-motion-ms) * 0.65) ease,
                filter calc(var(--viz-motion-ms) * 0.9) ease;
            z-index: 10;
        }
        .viz-grid.viz-grid-vertical .viz-bar {
            height: 34px;
            min-height: 26px;
            max-height: 40px;
            width: auto;
            border-radius: 14px;
            flex-direction: row;
            justify-content: flex-start;
            padding: 0 12px;
        }
        .viz-bar::after {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: inherit;
            background: linear-gradient(180deg, rgba(255,255,255,0.34), rgba(255,255,255,0) 32%);
            pointer-events: none;
        }
        .viz-bar::before {
            content: "";
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 16px;
            height: 6px;
            background: rgba(255, 255, 255, 0.82);
            border-radius: 999px;
            box-shadow: 0 2px 6px rgba(148, 163, 184, 0.24);
        }
        .viz-grid.viz-grid-vertical .viz-bar::after,
        .viz-grid.viz-grid-vertical .viz-bar::before {
            display: none;
        }
        .viz-grid.viz-grid-vertical::after {
            display: none;
        }
        .viz-bar-label {
            position: absolute;
            bottom: -11px;
            left: 50%;
            transform: translateX(-50%);
            min-width: 40px;
            padding: 6px 10px;
            border-radius: 12px;
            background: linear-gradient(180deg, rgba(255,255,255,0.98), rgba(236, 245, 255, 0.95));
            border: 1px solid rgba(173, 196, 224, 0.75);
            box-shadow: 0 12px 20px rgba(16, 35, 62, 0.1);
            color: #30445f;
            font-size: 11px;
            line-height: 1;
            text-align: center;
        }
        .viz-bar.active {
            transform: scale(1.02) translateY(-3px);
            z-index: 20;
            box-shadow: 
                0 0 0 3px rgba(44, 123, 255, 0.18),
                0 16px 28px rgba(44, 123, 255, 0.18);
        }
        .viz-bar.comparing {
            transform: scale(1.01) translateY(-1px);
            box-shadow: 
                0 0 0 3px rgba(25, 194, 201, 0.18),
                0 12px 22px rgba(25, 194, 201, 0.18);
        }
        .viz-bar.sorted {
            opacity: 0.92;
            transform: translateY(0);
            box-shadow: 
                0 0 0 2px rgba(16, 185, 129, 0.14),
                inset 0 -4px 0 rgba(16, 185, 129, 0.2);
        }
        .viz-bar.swapping {
            animation: swap calc(var(--viz-motion-ms) * 1.05) cubic-bezier(0.22, 1, 0.36, 1);
        }
        .viz-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            justify-content: center;
            font-size: 0.78rem;
            color: #475569;
        }
        .viz-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.3rem 0.6rem;
            border-radius: 999px;
            border: 1px solid rgba(148, 172, 201, 0.45);
            background: #ffffff;
        }
        .viz-legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        @keyframes swap {
            0%, 100% { transform: translateY(0) scale(1); }
            35% { transform: translateY(-8px) scale(1.02); }
            70% { transform: translateY(-3px) scale(1.01); }
        }
        .graph-node {
            position: absolute;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: white;
            border: 3px solid #2C7BFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            color: #10233E;
            box-shadow: 0 6px 16px rgba(44, 123, 255, 0.3);
            z-index: 30;
            transition: all 0.9s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .graph-node.visited {
            background: #2C7BFF;
            color: white;
            border-color: #1a68e8;
            box-shadow: 0 0 0 4px rgba(44, 123, 255, 0.25), 0 8px 20px rgba(44, 123, 255, 0.4);
        }
        .graph-node.current {
            background: #19C2C9;
            color: white;
            border-color: #14a5ac;
            box-shadow: 0 0 0 4px rgba(25, 194, 201, 0.3), 0 8px 20px rgba(25, 194, 201, 0.4);
            transform: scale(1.08);
        }
        .graph-edge {
            position: absolute;
            height: 3px;
            background: rgba(148, 172, 201, 0.7);
            transform-origin: left center;
            z-index: 5;
            transition: all 0.85s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .graph-edge.active {
            background: linear-gradient(90deg, #2C7BFF, #19C2C9);
            box-shadow: 0 0 8px rgba(44, 123, 255, 0.5);
            height: 5px;
        }
        .tree-node {
            position: absolute;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: white;
            border: 3px solid #7C5CFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 18px;
            color: #10233E;
            box-shadow: 0 6px 16px rgba(124, 92, 255, 0.25);
            z-index: 30;
            transition: all 0.9s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .tree-node.visited {
            background: #7C5CFF;
            color: white;
            border-color: #6546e0;
            box-shadow: 0 0 0 4px rgba(124, 92, 255, 0.25), 0 8px 20px rgba(124, 92, 255, 0.4);
        }
        .tree-node.current {
            transform: scale(1.08) rotate(1deg);
            box-shadow: 0 0 0 4px rgba(255, 138, 91, 0.35), 0 10px 25px rgba(255, 138, 91, 0.5);
            border-color: #ff8a5b;
        }
        .tree-edge {
            position: absolute;
            height: 3px;
            background: rgba(148, 172, 201, 0.6);
            transform-origin: left center;
            z-index: 5;
            transition: all 0.85s cubic-bezier(0.22, 1, 0.36, 1);
        }
        .structure-wrap {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            gap: 16px;
            padding: 12px;
        }
        .stack,
        .queue {
            display: flex;
            gap: 8px;
        }
        .stack {
            flex-direction: column-reverse;
            align-items: center;
        }
        .stack-slot,
        .queue-slot {
            width: 64px;
            height: 38px;
            border-radius: 12px;
            background: #ffffff;
            border: 2px solid rgba(44, 123, 255, 0.2);
            box-shadow: 0 6px 14px rgba(16, 35, 62, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #10233E;
        }
        .stack-slot.active,
        .queue-slot.active {
            border-color: #2C7BFF;
            box-shadow: 0 0 0 3px rgba(44, 123, 255, 0.2);
        }
        .hash-table {
            width: 100%;
            display: grid;
            gap: 10px;
        }
        .hash-bucket {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid rgba(148, 172, 201, 0.45);
        }
        .hash-bucket.active {
            border-color: #19C2C9;
            box-shadow: 0 0 0 3px rgba(25, 194, 201, 0.2);
        }
        .hash-label {
            font-size: 12px;
            font-weight: 800;
            color: #64748b;
            width: 28px;
            text-align: center;
        }
        .hash-item {
            padding: 6px 10px;
            border-radius: 10px;
            background: rgba(44, 123, 255, 0.12);
            color: #1e3a8a;
            font-weight: 700;
            font-size: 13px;
        }
        .algo-tab {
            position: relative;
            padding: 12px 20px;
            border-radius: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            text-align: center;
        }
        .algo-tab.active {
            background: white;
            box-shadow: 0 8px 24px rgba(44, 123, 255, 0.25);
            transform: translateY(-2px);
        }
        .algo-tab.active::after {
            content: "";
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%);
            width: 28px;
            height: 6px;
            background: #2C7BFF;
            border-radius: 3px;
        }
        .viz-control {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 18px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 800;
            transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            min-width: 110px;
        }
        .viz-control.primary {
            background: linear-gradient(135deg, #2C7BFF, #1968ec);
            color: white;
            box-shadow: 0 6px 18px rgba(44, 123, 255, 0.35);
        }
        .viz-control.primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(44, 123, 255, 0.45);
        }
        .viz-control.secondary {
            background: white;
            color: #38557C;
            border: 2px solid rgba(167, 183, 211, 0.75);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }
        .viz-control.secondary:hover {
            border-color: rgba(44, 123, 255, 0.55);
            color: #163765;
            transform: translateY(-1px);
        }
        .viz-control:disabled {
            opacity: 0.55;
            cursor: not-allowed;
            transform: none !important;
        }
        .viz-code {
            font-family: 'Menlo', 'Consolas', monospace;
            font-size: 14px;
            line-height: 1.5;
            border-radius: 18px;
            padding: 16px;
            background: rgba(16, 35, 62, 0.03);
            border: 1px solid rgba(167, 183, 211, 0.3);
            transition: all 0.25s ease;
        }
        .viz-code-line {
            padding: 4px 0;
            position: relative;
            transition: all 0.2s ease;
        }
        .viz-code-line.active {
            background: rgba(44, 123, 255, 0.12);
            border-left: 3px solid #2C7BFF;
            margin-left: -3px;
            padding-left: 13px;
            font-weight: 600;
            color: #163765;
        }
        .viz-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 999px;
            border: 1px solid rgba(167, 183, 211, 0.55);
            background: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            font-weight: 700;
            backdrop-filter: blur(8px);
        }
        .stats-card {
            border-radius: 24px;
            background: white;
            border: 1px solid rgba(167, 183, 211, 0.4);
            padding: 20px;
            transition: all 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(16, 35, 62, 0.12);
        }
        @media (max-width: 1024px) {
            .viz-bar { min-width: 22px; max-width: 96px; font-size: 12px; }
            .graph-node { width: 46px; height: 46px; font-size: 16px; }
            .tree-node { width: 52px; height: 52px; font-size: 16px; }
        }
        @media (max-width: 768px) {
            .viz-bar { min-width: 18px; max-width: 72px; font-size: 11px; padding-bottom: 6px; }
            .algo-tabs { flex-wrap: wrap; gap: 8px; }
            .control-group { flex-wrap: wrap; gap: 10px; }
            .stats-grid { grid-template-columns: repeat(2, 1fr) !important; }
        }
        @media (max-width: 480px) {
            .viz-stage { border-radius: 24px; }
            .viz-bar { min-width: 16px; max-width: 54px; font-size: 10px; }
            .graph-node, .tree-node { width: 42px; height: 42px; font-size: 15px; }
            .viz-control { padding: 10px 16px; min-width: 95px; font-size: 13px; }
            .stats-grid { grid-template-columns: 1fr !important; }
        }
    </style>
</head>

<body class="text-gray-900">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <!-- Hero Section -->
        <section class="viz-shell mb-8 shadow-panel">
            <div class="relative px-5 py-7 sm:px-8 sm:py-9 lg:px-10 lg:py-10">
                <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-2xl">
                        <div class="flex flex-wrap items-center gap-3 mb-5">
                            <span class="viz-chip bg-tf.sky text-tf.blue">
                                <i class="fas fa-wave-square"></i>
                                <?= t('visualizations_heading') ?>
                            </span>
                            <span class="viz-chip bg-white text-tf.aqua border-tf.aqua/30">
                                <i class="fas fa-project-diagram"></i>
                                VisuAlgo-inspired
                            </span>
                        </div>

                        <h1 class="text-3xl font-extrabold tracking-tight text-tf.ink sm:text-4xl lg:text-5xl">
                            <?= htmlspecialchars($viz['hero_title']) ?>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-tf.blue to-tf.aqua">
                                <?= htmlspecialchars($viz['hero_title_emphasis']) ?>
                            </span>
                        </h1>

                        <p class="mt-5 max-w-xl text-base leading-7 text-slate-700 sm:text-lg">
                            <?= htmlspecialchars($viz['hero_desc']) ?>
                        </p>

                        <div class="mt-7 flex flex-wrap gap-3">
                            <button id="startDemo" class="inline-flex items-center gap-2.5 rounded-2xl bg-gradient-to-r from-tf.blue to-tf.aqua px-6 py-3.5 text-sm font-bold shadow-blue-500/25 transition-all hover:shadow-blue-500/40 transform hover:-translate-y-0.5">
                                <i class="fas fa-play-circle"></i>
                                <?= htmlspecialchars($viz['start_demo']) ?>
                            </button>
                            <a href="#algorithms" class="inline-flex items-center gap-2 rounded-2xl border-2 border-slate-200 bg-white px-6 py-3.5 text-sm font-bold text-slate-800 transition-all hover:border-slate-300 hover:bg-slate-50 hover:-translate-y-0.5">
                                <i class="fas fa-list"></i>
                                <?= htmlspecialchars($viz['all_algorithms']) ?>
                            </a>
                        </div>
                    </div>

                    <div class="w-full max-w-xl">
                        <div class="viz-stage p-4 sm:p-6">
                            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200/80 pb-4 mb-5">
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-[0.24em] text-slate-500"><?= htmlspecialchars($viz['live_demo']) ?></div>
                                    <div id="demoTitle" class="mt-1.5 text-xl font-extrabold text-tf.ink">Bubble Sort</div>
                                </div>
                                <div class="flex items-center gap-2.5 rounded-full bg-white/95 px-4 py-2.5 shadow-sm border border-slate-200">
                                    <span class="h-3 w-3 rounded-full bg-emerald-400 animate-pulse-slow"></span>
                                    <span id="algoStatus" class="text-sm font-bold text-slate-700"><?= htmlspecialchars($viz['status_ready']) ?></span>
                                </div>
                            </div>

                            <div id="visualizationContainer" class="viz-grid rounded-2xl border border-slate-200/70 bg-white/80 min-h-[320px] p-5 sm:p-6 transition-all duration-500">
                                <!-- Bars will be inserted here by JS -->
                            </div>

                            <div class="mt-6 flex flex-wrap justify-center gap-3 control-group">
                                <button id="playBtn" class="viz-control primary">
                                    <i class="fas fa-play"></i>
                                    <span class="hidden sm:inline"><?= htmlspecialchars($viz['control_start']) ?></span>
                                </button>
                                <button id="pauseBtn" class="viz-control secondary" disabled>
                                    <i class="fas fa-pause"></i>
                                    <span class="hidden sm:inline"><?= htmlspecialchars($viz['control_pause']) ?></span>
                                </button>
                                <button id="stepBtn" class="viz-control secondary">
                                    <i class="fas fa-step-forward"></i>
                                    <span class="hidden sm:inline"><?= htmlspecialchars($viz['control_step']) ?></span>
                                </button>
                                <button id="resetBtn" class="viz-control secondary">
                                    <i class="fas fa-undo"></i>
                                    <span class="hidden sm:inline"><?= htmlspecialchars($viz['control_reset']) ?></span>
                                </button>
                                <button id="shuffleBtn" class="viz-control secondary">
                                    <i class="fas fa-shuffle"></i>
                                    <span class="hidden sm:inline"><?= htmlspecialchars($viz['control_shuffle']) ?></span>
                                </button>
                            </div>

                            <div class="mt-4 flex flex-wrap items-center justify-center gap-3">
                                <label for="speedRange" class="text-sm font-semibold text-slate-600"><?= htmlspecialchars($viz['control_speed']) ?></label>
                                <input id="speedRange" type="range" min="200" max="1500" step="50" value="800" class="w-44 accent-blue-600">
                                <span id="speedValue" class="text-sm font-bold text-slate-700 min-w-[64px] text-center">1.0x</span>
                            </div>

                            <div class="mt-4 viz-legend">
                                <div class="viz-legend-item">
                                    <span class="viz-legend-dot" style="background:#2C7BFF;"></span> <?= htmlspecialchars($viz['legend_active']) ?>
                                </div>
                                <div class="viz-legend-item">
                                    <span class="viz-legend-dot" style="background:#19C2C9;"></span> <?= htmlspecialchars($viz['legend_compare']) ?>
                                </div>
                                <div class="viz-legend-item">
                                    <span class="viz-legend-dot" style="background:#10B981;"></span> <?= htmlspecialchars($viz['legend_sorted']) ?>
                                </div>
                            </div>

                            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4 stats-grid">
                                <div class="stats-card">
                                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500"><?= htmlspecialchars($viz['stat_comparisons']) ?></div>
                                    <div id="comparisons" class="mt-2 text-2xl font-extrabold text-tf.blue">0</div>
                                </div>
                                <div class="stats-card">
                                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500"><?= htmlspecialchars($viz['stat_swaps']) ?></div>
                                    <div id="swaps" class="mt-2 text-2xl font-extrabold text-tf.aqua">0</div>
                                </div>
                                <div class="stats-card">
                                    <div class="text-xs font-bold uppercase tracking-wider text-slate-500"><?= htmlspecialchars($viz['stat_complexity']) ?></div>
                                    <div id="complexityValue" class="mt-2 text-lg font-bold text-amber-600">O(nВІ)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Algorithms Catalog -->
        <section id="algorithms" class="viz-shell shadow-panel mb-10">
            <div class="px-5 py-7 sm:px-8 sm:py-9">
                <div class="text-center mb-10">
                    <div class="text-xs font-bold uppercase tracking-[0.24em] text-tf.blue mb-3"><?= htmlspecialchars($viz['algorithms_label']) ?></div>
                    <h2 class="text-3xl font-extrabold text-tf.ink"><?= htmlspecialchars($viz['algorithms_title']) ?></h2>
                    <p class="mt-3 max-w-2xl mx-auto text-slate-600">
                        <?= htmlspecialchars($viz['algorithms_desc']) ?>
                    </p>
                </div>

                <!-- Algorithm Tabs -->
                <div class="flex justify-center flex-wrap gap-2 mb-8 algo-tabs" id="algoTabs">
                    <div class="algo-tab bg-tf.sky text-tf.blue active" data-algo="sorting"><?= htmlspecialchars($viz['tab_sorting']) ?></div>
                    <div class="algo-tab bg-white text-slate-700" data-algo="graphs"><?= htmlspecialchars($viz['tab_graphs']) ?></div>
                    <div class="algo-tab bg-white text-slate-700" data-algo="trees"><?= htmlspecialchars($viz['tab_trees']) ?></div>
                    <div class="algo-tab bg-white text-slate-700" data-algo="structures"><?= htmlspecialchars($viz['tab_structures']) ?></div>
                </div>

                <!-- Algorithm Grid -->
                <div id="algoGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Sorting Algorithms -->
                    <div class="algo-card sorting active" data-algo-type="bubble">
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-slate-200 p-6 h-full flex flex-col">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="text-lg font-extrabold text-tf.ink">Bubble Sort</div>
                                    <div class="mt-1 text-slate-600"><?= htmlspecialchars($viz['bubble_desc']) ?></div>
                                </div>
                                <div class="rounded-2xl bg-blue-100 p-3 text-blue-600 flex-shrink-0">
                                    <i class="fas fa-arrow-up-short-wide text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-5 space-y-3 flex-grow">
                                <div class="viz-code rounded-xl p-3 bg-white/60">
                                    <div class="viz-code-line">for i = 0 to n-1</div>
                                    <div class="viz-code-line">  for j = 0 to n-i-2</div>
                                    <div class="viz-code-line active">    if A[j] > A[j+1]</div>
                                    <div class="viz-code-line">      swap(A[j], A[j+1])</div>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">O(nВІ)</span>
                                    <span class="px-2.5 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium"><?= htmlspecialchars($viz['tag_stable']) ?></span>
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium"><?= htmlspecialchars($viz['tag_adaptive']) ?></span>
                                </div>
                            </div>
                            <button class="mt-5 w-full viz-control secondary select-algo" data-algo="bubble">
                                <i class="fas fa-play"></i>
                                <?= htmlspecialchars($viz['button_launch']) ?>
                            </button>
                        </div>
                    </div>
                    
                    <div class="algo-card sorting active" data-algo-type="quick">
                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl border border-slate-200 p-6 h-full flex flex-col">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="text-lg font-extrabold text-tf.ink">Quick Sort</div>
                                    <div class="mt-1 text-slate-600"><?= htmlspecialchars($viz['quick_desc']) ?></div>
                                </div>
                                <div class="rounded-2xl bg-amber-100 p-3 text-amber-700 flex-shrink-0">
                                    <i class="fas fa-bolt text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-5 space-y-3 flex-grow">
                                <div class="viz-code rounded-xl p-3 bg-white/60">
                                    <div class="viz-code-line">function quickSort(arr, low, high)</div>
                                    <div class="viz-code-line">  if low < high</div>
                                    <div class="viz-code-line active">    pi = partition(arr, low, high)</div>
                                    <div class="viz-code-line">    quickSort(arr, low, pi-1)</div>
                                    <div class="viz-code-line">    quickSort(arr, pi+1, high)</div>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium">O(n log n)</span>
                                    <span class="px-2.5 py-1 bg-red-100 text-red-800 rounded-full text-xs font-medium"><?= htmlspecialchars($viz['tag_unstable']) ?></span>
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium"><?= htmlspecialchars($viz['tag_inplace']) ?></span>
                                </div>
                            </div>
                            <button class="mt-5 w-full viz-control secondary select-algo" data-algo="quick">
                                <i class="fas fa-play"></i>
                                <?= htmlspecialchars($viz['button_launch']) ?>
                            </button>
                        </div>
                    </div>
                    
                    <div class="algo-card sorting active" data-algo-type="merge">
                        <div class="bg-gradient-to-br from-emerald-50 to-cyan-50 rounded-2xl border border-slate-200 p-6 h-full flex flex-col">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="text-lg font-extrabold text-tf.ink">Merge Sort</div>
                                    <div class="mt-1 text-slate-600"><?= htmlspecialchars($viz['merge_desc']) ?></div>
                                </div>
                                <div class="rounded-2xl bg-emerald-100 p-3 text-emerald-700 flex-shrink-0">
                                    <i class="fas fa-code-merge text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-5 space-y-3 flex-grow">
                                <div class="viz-code rounded-xl p-3 bg-white/60">
                                    <div class="viz-code-line">function mergeSort(arr)</div>
                                    <div class="viz-code-line">  if length(arr) <= 1 return arr</div>
                                    <div class="viz-code-line active">  mid = length(arr) / 2</div>
                                    <div class="viz-code-line">  left = mergeSort(first half)</div>
                                    <div class="viz-code-line">  right = mergeSort(second half)</div>
                                    <div class="viz-code-line">  return merge(left, right)</div>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-medium">O(n log n)</span>
                                    <span class="px-2.5 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium"><?= htmlspecialchars($viz['tag_stable']) ?></span>
                                    <span class="px-2.5 py-1 bg-violet-100 text-violet-800 rounded-full text-xs font-medium"><?= htmlspecialchars($viz['tag_memory']) ?></span>
                                </div>
                            </div>
                            <button class="mt-5 w-full viz-control secondary select-algo" data-algo="merge">
                                <i class="fas fa-play"></i>
                                <?= htmlspecialchars($viz['button_launch']) ?>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Graph Algorithms (hidden by default) -->
                    <div class="algo-card graphs hidden" data-algo-type="bfs">
                        <div class="bg-gradient-to-br from-violet-50 to-fuchsia-50 rounded-2xl border border-slate-200 p-6 h-full flex flex-col">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="text-lg font-extrabold text-tf.ink">BFS</div>
                                    <div class="mt-1 text-slate-600"><?= htmlspecialchars($viz['bfs_desc']) ?></div>
                                </div>
                                <div class="rounded-2xl bg-violet-100 p-3 text-violet-700 flex-shrink-0">
                                    <i class="fas fa-project-diagram text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-5 space-y-3 flex-grow">
                                <div class="viz-code rounded-xl p-3 bg-white/60">
                                    <div class="viz-code-line">BFS(G, start):</div>
                                    <div class="viz-code-line">  queue в†ђ {start}</div>
                                    <div class="viz-code-line active">  visited[start] в†ђ true</div>
                                    <div class="viz-code-line">  while queue not empty:</div>
                                    <div class="viz-code-line">    v в†ђ queue.dequeue()</div>
                                    <div class="viz-code-line">    for each neighbor u of v:</div>
                                    <div class="viz-code-line">      if not visited[u]:</div>
                                    <div class="viz-code-line">        visited[u] в†ђ true</div>
                                    <div class="viz-code-line">        queue.enqueue(u)</div>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="px-2.5 py-1 bg-violet-100 text-violet-800 rounded-full text-xs font-medium">O(V+E)</span>
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium"><?= htmlspecialchars($viz['tag_shortest']) ?></span>
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium"><?= htmlspecialchars($viz['tag_queue']) ?></span>
                                </div>
                            </div>
                            <button class="mt-5 w-full viz-control secondary select-algo" data-algo="bfs">
                                <i class="fas fa-play"></i>
                                <?= htmlspecialchars($viz['button_launch']) ?>
                            </button>
                        </div>
                    </div>
                    
                    <div class="algo-card graphs hidden" data-algo-type="dfs">
                        <div class="bg-gradient-to-br from-fuchsia-50 to-pink-50 rounded-2xl border border-slate-200 p-6 h-full flex flex-col">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="text-lg font-extrabold text-tf.ink">DFS</div>
                                    <div class="mt-1 text-slate-600"><?= htmlspecialchars($viz['dfs_desc']) ?></div>
                                </div>
                                <div class="rounded-2xl bg-fuchsia-100 p-3 text-fuchsia-700 flex-shrink-0">
                                    <i class="fas fa-sitemap text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-5 space-y-3 flex-grow">
                                <div class="viz-code rounded-xl p-3 bg-white/60">
                                    <div class="viz-code-line">DFS(G, v):</div>
                                    <div class="viz-code-line active">  visited[v] в†ђ true</div>
                                    <div class="viz-code-line">  for each neighbor u of v:</div>
                                    <div class="viz-code-line">    if not visited[u]:</div>
                                    <div class="viz-code-line">      DFS(G, u)</div>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="px-2.5 py-1 bg-fuchsia-100 text-fuchsia-800 rounded-full text-xs font-medium">O(V+E)</span>
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-medium"><?= htmlspecialchars($viz['tag_recursion']) ?></span>
                                    <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full text-xs font-medium"><?= htmlspecialchars($viz['tag_stack']) ?></span>
                                </div>
                            </div>
                            <button class="mt-5 w-full viz-control secondary select-algo" data-algo="dfs">
                                <i class="fas fa-play"></i>
                                <?= htmlspecialchars($viz['button_launch']) ?>
                            </button>
                        </div>
                    </div>
                    
                    <div class="algo-card graphs hidden" data-algo-type="dijkstra">
                        <div class="bg-gradient-to-br from-rose-50 to-red-50 rounded-2xl border border-slate-200 p-6 h-full flex flex-col">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="text-lg font-extrabold text-tf.ink">Dijkstra</div>
                                    <div class="mt-1 text-slate-600"><?= htmlspecialchars($viz['dijkstra_desc']) ?></div>
                                </div>
                                <div class="rounded-2xl bg-rose-100 p-3 text-rose-700 flex-shrink-0">
                                    <i class="fas fa-route text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-5 space-y-3 flex-grow">
                                <div class="viz-code rounded-xl p-3 bg-white/60">
                                    <div class="viz-code-line">Dijkstra(G, start):</div>
                                    <div class="viz-code-line">  dist[] в†ђ в€ћ</div>
                                    <div class="viz-code-line active">  dist[start] в†ђ 0</div>
                                    <div class="viz-code-line">  PQ в†ђ {start with dist 0}</div>
                                    <div class="viz-code-line">  while PQ not empty:</div>
                                    <div class="viz-code-line">    u в†ђ PQ.extractMin()</div>
                                    <div class="viz-code-line">    for each neighbor v of u:</div>
                                    <div class="viz-code-line">      alt = dist[u] + weight(u,v)</div>
                                    <div class="viz-code-line">      if alt < dist[v]:</div>
                                    <div class="viz-code-line">        dist[v] = alt</div>
                                    <div class="viz-code-line">        PQ.decreaseKey(v, alt)</div>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    <span class="px-2.5 py-1 bg-rose-100 text-rose-800 rounded-full text-xs font-medium">O((V+E)log V)</span>
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-medium"><?= htmlspecialchars($viz['tag_priority_queue']) ?></span>
                                    <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium"><?= htmlspecialchars($viz['tag_nonnegative']) ?></span>
                                </div>
                            </div>
                            <button class="mt-5 w-full viz-control secondary select-algo" data-algo="dijkstra">
                                <i class="fas fa-play"></i>
                                <?= htmlspecialchars($viz['button_launch']) ?>
                            </button>
                        </div>
                    </div>
                    
                    <div class="algo-card trees hidden" data-algo-type="bst">
                        <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-2xl border border-slate-200 p-6 h-full flex flex-col">
                            <div class="text-lg font-extrabold text-tf.ink">Binary Search Tree</div>
                            <p class="mt-2 text-slate-600 flex-grow"><?= htmlspecialchars($viz['bst_desc']) ?></p>
                            <button class="mt-5 w-full viz-control secondary select-algo" data-algo="bst">
                                <i class="fas fa-play"></i>
                                <?= htmlspecialchars($viz['button_launch']) ?>
                            </button>
                        </div>
                    </div>

                    <div class="algo-card trees hidden" data-algo-type="avl">
                        <div class="bg-gradient-to-br from-indigo-50 to-violet-50 rounded-2xl border border-slate-200 p-6 h-full flex flex-col">
                            <div class="text-lg font-extrabold text-tf.ink">AVL Tree</div>
                            <p class="mt-2 text-slate-600 flex-grow"><?= htmlspecialchars($viz['avl_desc']) ?></p>
                            <button class="mt-5 w-full viz-control secondary select-algo" data-algo="avl">
                                <i class="fas fa-play"></i>
                                <?= htmlspecialchars($viz['button_launch']) ?>
                            </button>
                        </div>
                    </div>

                    <div class="algo-card structures hidden" data-algo-type="stack">
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border border-slate-200 p-6 h-full flex flex-col">
                            <div class="text-lg font-extrabold text-tf.ink">Stack (LIFO)</div>
                            <p class="mt-2 text-slate-600 flex-grow"><?= htmlspecialchars($viz['stack_desc']) ?></p>
                            <button class="mt-5 w-full viz-control secondary select-algo" data-algo="stack">
                                <i class="fas fa-play"></i>
                                <?= htmlspecialchars($viz['button_launch']) ?>
                            </button>
                        </div>
                    </div>

                    <div class="algo-card structures hidden" data-algo-type="queue">
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl border border-slate-200 p-6 h-full flex flex-col">
                            <div class="text-lg font-extrabold text-tf.ink">Queue (FIFO)</div>
                            <p class="mt-2 text-slate-600 flex-grow"><?= htmlspecialchars($viz['queue_desc']) ?></p>
                            <button class="mt-5 w-full viz-control secondary select-algo" data-algo="queue">
                                <i class="fas fa-play"></i>
                                <?= htmlspecialchars($viz['button_launch']) ?>
                            </button>
                        </div>
                    </div>

                    <div class="algo-card structures hidden" data-algo-type="hash">
                        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-2xl border border-slate-200 p-6 h-full flex flex-col">
                            <div class="text-lg font-extrabold text-tf.ink">Hash Table</div>
                            <p class="mt-2 text-slate-600 flex-grow"><?= htmlspecialchars($viz['hash_desc']) ?></p>
                            <button class="mt-5 w-full viz-control secondary select-algo" data-algo="hash">
                                <i class="fas fa-play"></i>
                                <?= htmlspecialchars($viz['button_launch']) ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Educational Section -->
        <section class="viz-shell shadow-panel">
            <div class="px-5 py-7 sm:px-8 sm:py-9 max-w-4xl mx-auto">
                <div class="text-center mb-10">
                    <div class="text-xs font-bold uppercase tracking-[0.24em] text-tf.aqua mb-3"><?= htmlspecialchars($viz['learning_label']) ?></div>
                    <h2 class="text-3xl font-extrabold text-tf.ink"><?= htmlspecialchars($viz['learning_title']) ?></h2>
                    <p class="mt-3 text-slate-600">
                        <?= htmlspecialchars($viz['learning_desc']) ?>
                    </p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/70 text-center hover:shadow-lg transition-shadow">
                        <div class="mx-auto w-16 h-16 rounded-2xl bg-tf.sky flex items-center justify-center text-tf.blue text-2xl font-bold mb-4">
                            1
                        </div>
                        <h3 class="text-xl font-bold text-tf.ink mb-3"><?= htmlspecialchars($viz['learning_card1_title']) ?></h3>
                        <p class="text-slate-600">
                            <?= htmlspecialchars($viz['learning_card1_body']) ?>
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/70 text-center hover:shadow-lg transition-shadow">
                        <div class="mx-auto w-16 h-16 rounded-2xl bg-tf.sky flex items-center justify-center text-tf.blue text-2xl font-bold mb-4">
                            2
                        </div>
                        <h3 class="text-xl font-bold text-tf.ink mb-3"><?= htmlspecialchars($viz['learning_card2_title']) ?></h3>
                        <p class="text-slate-600">
                            <?= htmlspecialchars($viz['learning_card2_body']) ?>
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/70 text-center hover:shadow-lg transition-shadow">
                        <div class="mx-auto w-16 h-16 rounded-2xl bg-tf.sky flex items-center justify-center text-tf.blue text-2xl font-bold mb-4">
                            3
                        </div>
                        <h3 class="text-xl font-bold text-tf.ink mb-3"><?= htmlspecialchars($viz['learning_card3_title']) ?></h3>
                        <p class="text-slate-600">
                            <?= htmlspecialchars($viz['learning_card3_body']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php
    $footerContext = 'visualizations';
    include __DIR__ . '/../includes/footer.php';
    ?>
    
    <script>
        // ======================
        // CORE VISUALIZATION ENGINE
        // ======================
        const vizI18n = <?= tfSafeJson($viz, JSON_UNESCAPED_UNICODE) ?>;
        const vizFormat = (template, vars = {}) => String(template || '').replace(/\{(\w+)\}/g, (_, key) => (vars[key] ?? `{${key}}`));

        class AlgorithmVisualizer {
            constructor(containerId) {
                this.container = document.getElementById(containerId);
                this.state = null;
                this.timer = null;
                this.speed = 800; // ms per step
                this.algorithm = 'bubble';
                this.stats = { comparisons: 0, swaps: 0 };
                this.baseArrays = {
                    bubble: [28, 45, 16, 62, 37, 53, 21],
                    quick: [33, 15, 47, 28, 52, 19, 41],
                    merge: [42, 27, 58, 19, 36, 51, 24]
                };
                this.algorithmMeta = {
                    bubble: { title: vizI18n.algo_bubble, complexity: 'O(nВІ)', status: vizI18n.algo_bubble },
                    quick: { title: vizI18n.algo_quick, complexity: 'O(n log n)', status: vizI18n.algo_quick },
                    merge: { title: vizI18n.algo_merge, complexity: 'O(n log n)', status: vizI18n.algo_merge },
                    bfs: { title: vizI18n.algo_bfs, complexity: 'O(V+E)', status: vizI18n.algo_bfs },
                    dfs: { title: vizI18n.algo_dfs, complexity: 'O(V+E)', status: vizI18n.algo_dfs },
                    dijkstra: { title: vizI18n.algo_dijkstra, complexity: 'O((V+E) log V)', status: vizI18n.algo_dijkstra },
                    bst: { title: vizI18n.algo_bst, complexity: 'O(log n)', status: vizI18n.algo_bst },
                    avl: { title: vizI18n.algo_avl, complexity: 'O(log n)', status: vizI18n.algo_avl },
                    stack: { title: vizI18n.algo_stack, complexity: 'O(1)', status: vizI18n.algo_stack },
                    queue: { title: vizI18n.algo_queue, complexity: 'O(1)', status: vizI18n.algo_queue },
                    hash: { title: vizI18n.algo_hash, complexity: 'O(1) avg', status: vizI18n.algo_hash }
                };
                this.palette = [
                    ['#FFE2D7', '#FF8A5B'],
                    ['#FFECA8', '#FFD166'],
                    ['#C9F7F1', '#19C2C9'],
                    ['#D6E8FF', '#2C7BFF'],
                    ['#D5F7E6', '#10B981'],
                    ['#E6E0FF', '#7C5CFF'],
                    ['#FFE0FB', '#E94BFC'],
                    ['#D4E7FF', '#3B82F6']
                ];
                
                this.algorithms = {
                    bubble: this.bubbleSort.bind(this),
                    quick: this.quickSort.bind(this),
                    merge: this.mergeSort.bind(this),
                    bfs: this.bfs.bind(this),
                    dfs: this.dfs.bind(this),
                    dijkstra: this.dijkstra.bind(this),
                    bst: this.bstInsert.bind(this),
                    avl: this.avlSteps.bind(this),
                    stack: this.stackOps.bind(this),
                    queue: this.queueOps.bind(this),
                    hash: this.hashOps.bind(this)
                };
                
                this.initEventListeners();
                this.reset('bubble');
            }
            
            initEventListeners() {
                document.getElementById('playBtn')?.addEventListener('click', () => this.play());
                document.getElementById('pauseBtn')?.addEventListener('click', () => this.pause());
                document.getElementById('stepBtn')?.addEventListener('click', () => {
                    this.pause();
                    this.step();
                });
                document.getElementById('resetBtn')?.addEventListener('click', () => this.reset(this.algorithm));
                document.getElementById('shuffleBtn')?.addEventListener('click', () => this.shuffleCurrentDataset());
                document.getElementById('speedRange')?.addEventListener('input', (e) => {
                    const value = parseInt(e.target.value, 10);
                    if (!Number.isNaN(value)) this.setSpeed(value);
                });
                document.getElementById('startDemo')?.addEventListener('click', () => {
                    this.reset('bubble');
                    document.querySelector('.algo-card[data-algo-type="bubble"] .select-algo').scrollIntoView({behavior: 'smooth', block: 'nearest'});
                });
                
                // Algorithm selection
                document.querySelectorAll('.select-algo').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const algo = e.currentTarget.dataset.algo;
                        this.reset(algo);
                        this.play();
                        
                        // Scroll to visualization
                        document.querySelector('.viz-shell').scrollIntoView({behavior: 'smooth', block: 'start'});
                    });
                });
                
                // Tab switching
                document.querySelectorAll('.algo-tab').forEach(tab => {
                    tab.addEventListener('click', (e) => {
                        const algoType = e.currentTarget.dataset.algo;
                        
                        // Update tabs
                        document.querySelectorAll('.algo-tab').forEach(t => t.classList.remove('active'));
                        e.currentTarget.classList.add('active');
                        
                        // Show/hide algorithm cards
                        document.querySelectorAll('.algo-card').forEach(card => {
                            card.classList.add('hidden');
                            if (card.classList.contains(algoType)) {
                                card.classList.remove('hidden');
                            }
                        });
                    });
                });
            }
            
            reset(algorithm) {
                this.pause();
                this.algorithm = algorithm;
                this.stats = { comparisons: 0, swaps: 0 };
                this.updateStats();
                
                if (algorithm === 'bubble') {
                    this.state = this.createBubbleState(this.baseArrays.bubble);
                } else if (algorithm === 'quick') {
                    this.state = this.createQuickState(this.baseArrays.quick);
                } else if (algorithm === 'merge') {
                    this.state = this.createMergeState(this.baseArrays.merge);
                } else if (algorithm === 'bfs') {
                    this.state = this.createBFSState();
                } else if (algorithm === 'dfs') {
                    this.state = this.createDFSState();
                } else if (algorithm === 'dijkstra') {
                    this.state = this.createDijkstraState();
                } else if (algorithm === 'bst') {
                    this.state = this.createBSTState();
                } else if (algorithm === 'avl') {
                    this.state = this.createAVLState();
                } else if (algorithm === 'stack') {
                    this.state = this.createStackState();
                } else if (algorithm === 'queue') {
                    this.state = this.createQueueState();
                } else if (algorithm === 'hash') {
                    this.state = this.createHashState();
                }
                
                this.render();
                this.updateAlgorithmUI();
                document.getElementById('algoStatus').textContent = vizI18n.status_ready;
                document.getElementById('playBtn').disabled = false;
                document.getElementById('pauseBtn').disabled = true;
            }

            setSpeed(ms) {
                this.speed = Math.max(200, Math.min(1500, ms));
                const speedValue = document.getElementById('speedValue');
                if (speedValue) {
                    const multiplier = (800 / this.speed);
                    speedValue.textContent = `${multiplier.toFixed(1)}x`;
                }
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = setInterval(() => this.step(), this.speed);
                }
            }
            
            play() {
                if (this.timer || !this.state || this.state.done) return;
                document.getElementById('playBtn').disabled = true;
                document.getElementById('pauseBtn').disabled = false;
                document.getElementById('algoStatus').textContent = vizFormat(vizI18n.status_running, { name: this.getAlgorithmLabel(this.algorithm) });
                this.timer = setInterval(() => this.step(), this.speed);
            }
            
            pause() {
                if (this.timer) {
                    clearInterval(this.timer);
                    this.timer = null;
                    document.getElementById('playBtn').disabled = false;
                    document.getElementById('pauseBtn').disabled = true;
                    document.getElementById('algoStatus').textContent = vizI18n.status_pause;
                }
            }

            randomArray(length = 7, min = 12, max = 68) {
                const result = [];
                for (let i = 0; i < length; i++) {
                    result.push(Math.floor(Math.random() * (max - min + 1)) + min);
                }
                return result;
            }

            shuffleCurrentDataset() {
                if (!['bubble', 'quick', 'merge'].includes(this.algorithm)) return;
                this.baseArrays[this.algorithm] = this.randomArray();
                this.reset(this.algorithm);
                const status = document.getElementById('algoStatus');
                if (status) status.textContent = vizI18n.status_new_data;
            }
            
            step() {
                if (!this.state || this.state.done) {
                    this.pause();
                    document.getElementById('algoStatus').textContent = vizI18n.status_done;
                    return;
                }
                
                if (this.algorithms[this.algorithm]) {
                    this.algorithms[this.algorithm]();
                }
                
                this.render();
                
                if (this.state.done) {
                    this.pause();
                    document.getElementById('algoStatus').textContent = vizI18n.status_completed;
                }
            }
            
            // ======================
            // ALGORITHM IMPLEMENTATIONS
            // ======================
            
            // Bubble Sort Implementation
            createBubbleState(inputValues) {
                const values = Array.isArray(inputValues) ? [...inputValues] : [...this.baseArrays.bubble];
                return {
                    values: [...values],
                    original: [...values],
                    i: 0,
                    j: 0,
                    active: [0, 1],
                    comparing: [0, 1],
                    swapping: null,
                    sortedFrom: values.length,
                    done: false,
                    phase: 'compare'
                };
            }
            
            bubbleSort() {
                const limit = this.state.values.length - 1 - this.state.i;
                
                if (this.state.phase === 'compare') {
                    this.stats.comparisons++;
                    this.updateStats();
                    
                    const left = this.state.values[this.state.j];
                    const right = this.state.values[this.state.j + 1];
                    this.state.comparing = [this.state.j, this.state.j + 1];
                    
                    if (left > right) {
                        // Prepare swap
                        this.state.swapping = [this.state.j, this.state.j + 1];
                        this.state.phase = 'swap';
                    } else if (this.state.j + 1 >= limit) {
                        // End of pass
                        this.state.sortedFrom = limit;
                        this.state.phase = 'mark';
                    } else {
                        this.state.j++;
                        this.state.active = [this.state.j, this.state.j + 1];
                    }
                    return;
                }
                
                if (this.state.phase === 'swap') {
                    this.stats.swaps++;
                    this.updateStats();
                    
                    const [a, b] = this.state.swapping;
                    [this.state.values[a], this.state.values[b]] = [this.state.values[b], this.state.values[a]];
                    
                    if (this.state.j + 1 >= limit) {
                        this.state.sortedFrom = limit;
                        this.state.phase = 'mark';
                    } else {
                        this.state.j++;
                        this.state.active = [this.state.j, this.state.j + 1];
                        this.state.phase = 'compare';
                    }
                    this.state.swapping = null;
                    return;
                }
                
                if (this.state.phase === 'mark') {
                    this.state.i++;
                    this.state.j = 0;
                    
                    if (this.state.i >= this.state.values.length - 1) {
                        this.state.sortedFrom = 0;
                        this.state.active = [];
                        this.state.comparing = [];
                        this.state.done = true;
                        return;
                    }
                    
                    this.state.active = [0, 1];
                    this.state.comparing = [0, 1];
                    this.state.phase = 'compare';
                }
            }
            
            // Quick Sort Implementation (simplified for visualization)
            createQuickState(inputValues) {
                const values = Array.isArray(inputValues) ? [...inputValues] : [...this.baseArrays.quick];
                return {
                    values: [...values],
                    original: [...values],
                    stack: [{low: 0, high: values.length - 1}],
                    current: null,
                    active: [],
                    comparing: [],
                    swapping: null,
                    sortedIndices: [],
                    done: false,
                    phase: 'partition'
                };
            }
            
            quickSort() {
                if (this.state.phase === 'partition') {
                    if (!this.state.current) {
                        while (this.state.stack.length > 0) {
                            const range = this.state.stack.pop();
                            if (range.low >= range.high) {
                                if (range.low === range.high && !this.state.sortedIndices.includes(range.low)) {
                                    this.state.sortedIndices.push(range.low);
                                }
                            } else {
                                this.state.current = {
                                    low: range.low,
                                    high: range.high,
                                    pivotIndex: range.high,
                                    i: range.low - 1,
                                    j: range.low
                                };
                                this.state.active = [range.high];
                                this.state.comparing = [];
                                break;
                            }
                        }
                        
                        if (!this.state.current) {
                            this.state.done = true;
                            this.state.active = [];
                            this.state.comparing = [];
                        }
                        return;
                    }
                    
                    const {high, pivotIndex, j} = this.state.current;
                    if (j < high) {
                        this.stats.comparisons++;
                        this.updateStats();
                        this.state.comparing = [j, pivotIndex];
                        
                        if (this.state.values[j] <= this.state.values[pivotIndex]) {
                            this.state.current.i++;
                            if (this.state.current.i !== j) {
                                this.state.swapping = [this.state.current.i, j];
                                this.state.phase = 'swap';
                                return;
                            }
                        }
                        this.state.current.j++;
                        return;
                    }
                    
                    // Final swap for pivot
                    this.state.swapping = [this.state.current.i + 1, pivotIndex];
                    this.state.phase = 'swap-pivot';
                    return;
                }
                
                if (this.state.phase === 'swap' || this.state.phase === 'swap-pivot') {
                    this.stats.swaps++;
                    this.updateStats();
                    
                    const [a, b] = this.state.swapping;
                    [this.state.values[a], this.state.values[b]] = [this.state.values[b], this.state.values[a]];
                    
                    if (this.state.phase === 'swap-pivot') {
                        const pivotFinalIndex = this.state.current.i + 1;
                        if (!this.state.sortedIndices.includes(pivotFinalIndex)) {
                            this.state.sortedIndices.push(pivotFinalIndex);
                        }
                        
                        // Push subarrays to stack (right first, then left for LIFO order)
                        if (pivotFinalIndex + 1 < this.state.current.high) {
                            this.state.stack.push({
                                low: pivotFinalIndex + 1,
                                high: this.state.current.high
                            });
                        }
                        if (pivotFinalIndex - 1 > this.state.current.low) {
                            this.state.stack.push({
                                low: this.state.current.low,
                                high: pivotFinalIndex - 1
                            });
                        }
                        
                        this.state.current = null;
                        this.state.phase = 'partition';
                    } else {
                        this.state.current.j++;
                        this.state.phase = 'partition';
                    }
                    
                    this.state.swapping = null;
                    this.state.comparing = [];
                    return;
                }
            }
            
            // Merge Sort Implementation (simplified)
            createMergeState(inputValues) {
                const values = Array.isArray(inputValues) ? [...inputValues] : [...this.baseArrays.merge];
                return {
                    values: [...values],
                    original: [...values],
                    n: values.length,
                    width: 1,
                    leftStart: 0,
                    currentMerge: null,
                    leftPart: [],
                    rightPart: [],
                    i: 0,
                    j: 0,
                    k: 0,
                    active: [],
                    comparing: [],
                    sortedIndices: [],
                    done: false,
                    phase: 'prepare'
                };
            }
            
            mergeSort() {
                if (this.state.phase === 'prepare') {
                    if (this.state.width >= this.state.n) {
                        this.state.done = true;
                        this.state.sortedIndices = Array.from({ length: this.state.n }, (_, idx) => idx);
                        this.state.active = [];
                        this.state.comparing = [];
                        return;
                    }

                    if (this.state.leftStart >= this.state.n - 1) {
                        this.state.width *= 2;
                        this.state.leftStart = 0;
                        return;
                    }

                    const low = this.state.leftStart;
                    const mid = Math.min(low + this.state.width - 1, this.state.n - 1);
                    const high = Math.min(low + (2 * this.state.width) - 1, this.state.n - 1);

                    if (mid >= high) {
                        this.state.leftStart += 2 * this.state.width;
                        return;
                    }

                    this.state.currentMerge = { low, mid, high };
                    this.state.leftPart = this.state.values.slice(low, mid + 1);
                    this.state.rightPart = this.state.values.slice(mid + 1, high + 1);
                    this.state.i = 0;
                    this.state.j = 0;
                    this.state.k = low;
                    this.state.active = [low, high];
                    this.state.comparing = [];
                    this.state.phase = 'merge';
                    return;
                }
                
                if (this.state.phase === 'merge') {
                    const current = this.state.currentMerge;
                    if (!current) {
                        this.state.phase = 'prepare';
                        return;
                    }

                    let chosenValue;
                    if (this.state.i < this.state.leftPart.length && this.state.j < this.state.rightPart.length) {
                        this.stats.comparisons++;
                        this.updateStats();
                        this.state.comparing = [
                            current.low + this.state.i,
                            current.mid + 1 + this.state.j
                        ];
                        
                        if (this.state.leftPart[this.state.i] <= this.state.rightPart[this.state.j]) {
                            chosenValue = this.state.leftPart[this.state.i];
                            this.state.i++;
                        } else {
                            chosenValue = this.state.rightPart[this.state.j];
                            this.state.j++;
                        }
                    } else if (this.state.i < this.state.leftPart.length) {
                        chosenValue = this.state.leftPart[this.state.i];
                        this.state.i++;
                        this.state.comparing = [];
                    } else if (this.state.j < this.state.rightPart.length) {
                        chosenValue = this.state.rightPart[this.state.j];
                        this.state.j++;
                        this.state.comparing = [];
                    } else {
                        this.state.currentMerge = null;
                        this.state.leftStart += 2 * this.state.width;
                        this.state.phase = 'prepare';
                        this.state.comparing = [];
                        return;
                    }

                    this.state.values[this.state.k] = chosenValue;
                    this.state.k++;
                    this.stats.swaps++;
                    this.updateStats();
                }
            }
            
            // Graph Algorithms (simplified representations)
            createBFSState() {
                return {
                    nodes: [
                        {id: 'A', x: 150, y: 100, visited: false, distance: 0},
                        {id: 'B', x: 300, y: 80, visited: false, distance: Infinity},
                        {id: 'C', x: 250, y: 220, visited: false, distance: Infinity},
                        {id: 'D', x: 450, y: 100, visited: false, distance: Infinity},
                        {id: 'E', x: 400, y: 220, visited: false, distance: Infinity}
                    ],
                    edges: [
                        {from: 'A', to: 'B', weight: 1},
                        {from: 'A', to: 'C', weight: 1},
                        {from: 'B', to: 'D', weight: 1},
                        {from: 'C', to: 'E', weight: 1},
                        {from: 'D', to: 'E', weight: 1}
                    ],
                    queue: ['A'],
                    current: null,
                    visited: ['A'],
                    done: false
                };
            }
            
            bfs() {
                if (this.state.queue.length === 0) {
                    this.state.done = true;
                    return;
                }
                
                this.state.current = this.state.queue.shift();
                
                // Find current node
                const currentNode = this.state.nodes.find(n => n.id === this.state.current);
                if (currentNode) currentNode.visited = true;
                
                // Find neighbors
                const neighbors = this.state.edges
                    .filter(e => e.from === this.state.current && !this.state.visited.includes(e.to))
                    .map(e => e.to);
                
                // Add unvisited neighbors to queue
                neighbors.forEach(neighborId => {
                    if (!this.state.queue.includes(neighborId) && !this.state.visited.includes(neighborId)) {
                        this.state.queue.push(neighborId);
                        this.state.visited.push(neighborId);
                        
                        // Update distance
                        const neighbor = this.state.nodes.find(n => n.id === neighborId);
                        if (neighbor) {
                            const current = this.state.nodes.find(n => n.id === this.state.current);
                            neighbor.distance = current.distance + 1;
                        }
                    }
                });
                
                // Check if done
                if (this.state.queue.length === 0 && neighbors.length === 0) {
                    this.state.done = true;
                }
            }
            
            createDFSState() {
                return {
                    nodes: [
                        {id: 'A', x: 150, y: 100, visited: false},
                        {id: 'B', x: 300, y: 80, visited: false},
                        {id: 'C', x: 250, y: 220, visited: false},
                        {id: 'D', x: 450, y: 100, visited: false},
                        {id: 'E', x: 400, y: 220, visited: false}
                    ],
                    edges: [
                        {from: 'A', to: 'B', weight: 1},
                        {from: 'A', to: 'C', weight: 1},
                        {from: 'B', to: 'D', weight: 1},
                        {from: 'C', to: 'E', weight: 1},
                        {from: 'D', to: 'E', weight: 1}
                    ],
                    stack: ['A'],
                    current: null,
                    visited: ['A'],
                    done: false
                };
            }
            
            dfs() {
                if (this.state.stack.length === 0) {
                    this.state.done = true;
                    return;
                }
                
                this.state.current = this.state.stack.pop();
                
                // Find current node
                const currentNode = this.state.nodes.find(n => n.id === this.state.current);
                if (currentNode) currentNode.visited = true;
                
                // Find unvisited neighbors
                const neighbors = this.state.edges
                    .filter(e => e.from === this.state.current && !this.state.visited.includes(e.to))
                    .map(e => e.to)
                    .reverse(); // Reverse to maintain visual order
                
                // Push unvisited neighbors to stack
                neighbors.forEach(neighborId => {
                    if (!this.state.visited.includes(neighborId)) {
                        this.state.stack.push(neighborId);
                        this.state.visited.push(neighborId);
                    }
                });
                
                // Check if done
                if (this.state.stack.length === 0 && neighbors.length === 0) {
                    this.state.done = true;
                }
            }
            
            createDijkstraState() {
                return {
                    nodes: [
                        {id: 'A', x: 150, y: 100, dist: 0, visited: false},
                        {id: 'B', x: 300, y: 80, dist: Infinity, visited: false},
                        {id: 'C', x: 250, y: 220, dist: Infinity, visited: false},
                        {id: 'D', x: 450, y: 100, dist: Infinity, visited: false},
                        {id: 'E', x: 400, y: 220, dist: Infinity, visited: false}
                    ],
                    edges: [
                        {from: 'A', to: 'B', weight: 4},
                        {from: 'A', to: 'C', weight: 2},
                        {from: 'B', to: 'D', weight: 5},
                        {from: 'C', to: 'B', weight: 1},
                        {from: 'C', to: 'D', weight: 8},
                        {from: 'C', to: 'E', weight: 10},
                        {from: 'D', to: 'E', weight: 2}
                    ],
                    unvisited: ['A', 'B', 'C', 'D', 'E'],
                    current: 'A',
                    done: false
                };
            }
            
            dijkstra() {
                if (this.state.unvisited.length === 0) {
                    this.state.done = true;
                    return;
                }
                
                // Mark current as visited
                const currentNode = this.state.nodes.find(n => n.id === this.state.current);
                if (currentNode) currentNode.visited = true;
                
                // Remove current from unvisited
                this.state.unvisited = this.state.unvisited.filter(id => id !== this.state.current);
                
                // Update distances to neighbors
                this.state.edges
                    .filter(e => e.from === this.state.current)
                    .forEach(edge => {
                        const neighbor = this.state.nodes.find(n => n.id === edge.to);
                        if (neighbor && !neighbor.visited) {
                            const newDist = currentNode.dist + edge.weight;
                            if (newDist < neighbor.dist) {
                                neighbor.dist = newDist;
                                this.stats.comparisons++;
                            }
                        }
                    });
                
                // Find next current (smallest unvisited distance)
                if (this.state.unvisited.length > 0) {
                    const next = this.state.unvisited.reduce((minId, id) => {
                        const node = this.state.nodes.find(n => n.id === id);
                        const minNode = this.state.nodes.find(n => n.id === minId);
                        return node.dist < minNode.dist ? id : minId;
                    }, this.state.unvisited[0]);
                    
                    this.state.current = next;
                } else {
                    this.state.done = true;
                }
                
                this.updateStats();
            }

            createBSTState() {
                return {
                    nodes: [
                        {id: '50', value: 50, x: 0.5, y: 0.12, inserted: false},
                        {id: '30', value: 30, x: 0.32, y: 0.32, inserted: false},
                        {id: '70', value: 70, x: 0.68, y: 0.32, inserted: false},
                        {id: '20', value: 20, x: 0.2, y: 0.52, inserted: false},
                        {id: '40', value: 40, x: 0.42, y: 0.52, inserted: false},
                        {id: '60', value: 60, x: 0.58, y: 0.52, inserted: false},
                        {id: '80', value: 80, x: 0.8, y: 0.52, inserted: false}
                    ],
                    edges: [
                        {from: '50', to: '30'},
                        {from: '50', to: '70'},
                        {from: '30', to: '20'},
                        {from: '30', to: '40'},
                        {from: '70', to: '60'},
                        {from: '70', to: '80'}
                    ],
                    order: ['50', '30', '70', '20', '40', '60', '80'],
                    step: 0,
                    current: null,
                    done: false
                };
            }

            bstInsert() {
                if (this.state.step >= this.state.order.length) {
                    this.state.done = true;
                    this.state.current = null;
                    return;
                }
                const id = this.state.order[this.state.step];
                const node = this.state.nodes.find(n => n.id === id);
                if (node) node.inserted = true;
                this.state.current = id;
                this.state.step += 1;
                this.stats.comparisons += 1;
                this.updateStats();
            }

            createAVLState() {
                const nodes = [
                    {id: '30', value: 30, x: 0.5, y: 0.12, inserted: true},
                    {id: '20', value: 20, x: 0.3, y: 0.32, inserted: true},
                    {id: '40', value: 40, x: 0.7, y: 0.32, inserted: true},
                    {id: '10', value: 10, x: 0.2, y: 0.52, inserted: true},
                    {id: '25', value: 25, x: 0.4, y: 0.52, inserted: true},
                    {id: '50', value: 50, x: 0.8, y: 0.52, inserted: true}
                ];
                const frames = [
                    {
                        '30': {x: 0.5, y: 0.12},
                        '20': {x: 0.3, y: 0.32},
                        '40': {x: 0.7, y: 0.32},
                        '10': {x: 0.2, y: 0.52},
                        '25': {x: 0.4, y: 0.52},
                        '50': {x: 0.8, y: 0.52}
                    },
                    {
                        '30': {x: 0.5, y: 0.12},
                        '20': {x: 0.25, y: 0.32},
                        '40': {x: 0.7, y: 0.32},
                        '10': {x: 0.15, y: 0.52},
                        '25': {x: 0.35, y: 0.52},
                        '50': {x: 0.8, y: 0.52}
                    },
                    {
                        '30': {x: 0.6, y: 0.12},
                        '20': {x: 0.3, y: 0.32},
                        '40': {x: 0.8, y: 0.32},
                        '10': {x: 0.2, y: 0.52},
                        '25': {x: 0.4, y: 0.52},
                        '50': {x: 0.9, y: 0.52}
                    }
                ];
                return {
                    nodes,
                    edges: [
                        {from: '30', to: '20'},
                        {from: '30', to: '40'},
                        {from: '20', to: '10'},
                        {from: '20', to: '25'},
                        {from: '40', to: '50'}
                    ],
                    frames,
                    frameIndex: 0,
                    current: '30',
                    done: false
                };
            }

            avlSteps() {
                if (this.state.frameIndex >= this.state.frames.length - 1) {
                    this.state.done = true;
                    this.state.current = null;
                    return;
                }
                this.state.frameIndex += 1;
                const frame = this.state.frames[this.state.frameIndex];
                this.state.nodes.forEach(node => {
                    if (frame[node.id]) {
                        node.x = frame[node.id].x;
                        node.y = frame[node.id].y;
                    }
                });
                this.state.current = this.state.frameIndex === 1 ? '20' : '40';
                this.stats.comparisons += 1;
                this.updateStats();
            }

            createStackState() {
                return {
                    items: [5, 12, 21],
                    ops: [
                        {type: 'push', value: 34},
                        {type: 'push', value: 8},
                        {type: 'pop'},
                        {type: 'push', value: 17},
                        {type: 'pop'}
                    ],
                    step: 0,
                    activeIndex: null,
                    done: false
                };
            }

            stackOps() {
                if (this.state.step >= this.state.ops.length) {
                    this.state.done = true;
                    this.state.activeIndex = null;
                    return;
                }
                const op = this.state.ops[this.state.step];
                if (op.type === 'push') {
                    this.state.items.push(op.value);
                    this.state.activeIndex = this.state.items.length - 1;
                    this.stats.swaps += 1;
                } else if (op.type === 'pop') {
                    this.state.items.pop();
                    this.state.activeIndex = this.state.items.length - 1;
                    this.stats.comparisons += 1;
                }
                this.state.step += 1;
                this.updateStats();
            }

            createQueueState() {
                return {
                    items: [11, 24, 37],
                    ops: [
                        {type: 'enqueue', value: 50},
                        {type: 'enqueue', value: 13},
                        {type: 'dequeue'},
                        {type: 'enqueue', value: 29},
                        {type: 'dequeue'}
                    ],
                    step: 0,
                    activeIndex: null,
                    done: false
                };
            }

            queueOps() {
                if (this.state.step >= this.state.ops.length) {
                    this.state.done = true;
                    this.state.activeIndex = null;
                    return;
                }
                const op = this.state.ops[this.state.step];
                if (op.type === 'enqueue') {
                    this.state.items.push(op.value);
                    this.state.activeIndex = this.state.items.length - 1;
                    this.stats.swaps += 1;
                } else if (op.type === 'dequeue') {
                    this.state.items.shift();
                    this.state.activeIndex = 0;
                    this.stats.comparisons += 1;
                }
                this.state.step += 1;
                this.updateStats();
            }

            createHashState() {
                return {
                    buckets: Array.from({ length: 6 }, () => []),
                    ops: [
                        {type: 'insert', key: 21},
                        {type: 'insert', key: 33},
                        {type: 'insert', key: 15},
                        {type: 'insert', key: 27},
                        {type: 'search', key: 33},
                        {type: 'remove', key: 21}
                    ],
                    step: 0,
                    activeBucket: null,
                    done: false
                };
            }

            hashOps() {
                if (this.state.step >= this.state.ops.length) {
                    this.state.done = true;
                    this.state.activeBucket = null;
                    return;
                }
                const op = this.state.ops[this.state.step];
                const bucket = op.key % this.state.buckets.length;
                this.state.activeBucket = bucket;
                const target = this.state.buckets[bucket];
                if (op.type === 'insert') {
                    target.push(op.key);
                    this.stats.swaps += 1;
                } else if (op.type === 'search') {
                    this.stats.comparisons += target.length;
                } else if (op.type === 'remove') {
                    const idx = target.indexOf(op.key);
                    if (idx >= 0) target.splice(idx, 1);
                    this.stats.comparisons += 1;
                }
                this.state.step += 1;
                this.updateStats();
            }
            
            // ======================
            // RENDERING
            // ======================
            
            getBarGradient(index) {
                const pair = this.palette[index % this.palette.length];
                return `linear-gradient(180deg, ${pair[0]}, ${pair[1]})`;
            }

            getAlgorithmLabel(algorithm) {
                return this.algorithmMeta[algorithm]?.status || algorithm;
            }

            updateAlgorithmUI() {
                const meta = this.algorithmMeta[this.algorithm];
                const titleEl = document.getElementById('demoTitle');
                const complexityEl = document.getElementById('complexityValue');
                const shuffleBtn = document.getElementById('shuffleBtn');
                if (meta && titleEl) titleEl.textContent = meta.title;
                if (meta && complexityEl) complexityEl.textContent = meta.complexity;
                if (shuffleBtn) {
                    const canShuffle = ['bubble', 'quick', 'merge'].includes(this.algorithm);
                    shuffleBtn.disabled = !canShuffle;
                }
            }
            
            render() {
                if (!this.container) return;
                
                // Clear container
                this.container.innerHTML = '';
                this.container.className = 'viz-grid rounded-2xl border border-slate-200/70 bg-white/80 min-h-[320px] p-5 sm:p-6 transition-all duration-500';
                this.container.removeAttribute('style');
                this.container.style.setProperty('--viz-motion-ms', `${Math.max(520, Math.round(this.speed * 0.92))}ms`);
                
                // Render based on algorithm type
                if (['bubble', 'quick', 'merge'].includes(this.algorithm)) {
                    this.container.classList.add('viz-grid-sorting');
                    this.renderArray();
                } else if (['bfs', 'dfs', 'dijkstra'].includes(this.algorithm)) {
                    this.renderGraph();
                } else if (['bst', 'avl'].includes(this.algorithm)) {
                    this.renderTree();
                } else if (['stack', 'queue', 'hash'].includes(this.algorithm)) {
                    this.renderStructure();
                }
            }
            
            renderArray() {
                const fragment = document.createDocumentFragment();
                const isVertical = this.container.classList.contains('viz-grid-vertical');
                const values = Array.isArray(this.state.values) ? this.state.values : [];
                const containerWidth = this.container.clientWidth || 640;
                const containerHeight = this.container.clientHeight || 320;
                const gap = window.innerWidth <= 480 ? 6 : window.innerWidth <= 768 ? 8 : 10;
                const availableWidth = Math.max(220, containerWidth - gap * Math.max(0, values.length - 1));
                const minBarWidth = isVertical
                    ? (window.innerWidth <= 480 ? 120 : window.innerWidth <= 768 ? 160 : 200)
                    : (window.innerWidth <= 480 ? 16 : window.innerWidth <= 768 ? 18 : window.innerWidth <= 1024 ? 22 : 24);
                const maxBarWidth = isVertical
                    ? Math.max(minBarWidth, containerWidth - 32)
                    : (window.innerWidth <= 480 ? 54 : window.innerWidth <= 768 ? 72 : window.innerWidth <= 1024 ? 96 : 132);
                
                const itemCount = Math.max(1, values.length);
                const columnWidth = Math.max(minBarWidth, Math.min(maxBarWidth, Math.floor(availableWidth / itemCount)));
                const totalValue = values.reduce((sum, value) => sum + Math.max(1, Number(value) || 0), 0);
                const maxValue = values.reduce((max, value) => Math.max(max, Math.max(1, Number(value) || 0)), 1);
                const availableHeight = Math.max(180, containerHeight - 84);

                this.state.values.forEach((value, index) => {
                    const bar = document.createElement('div');
                    const numericValue = Math.max(1, Number(value) || 0);
                    const height = Math.max(28, Math.round((numericValue / maxValue) * availableHeight));
                    const proportionalWidth = totalValue > 0 ? Math.round((Math.max(1, Number(value) || 0) / totalValue) * availableWidth) : minBarWidth;
                    
                    bar.className = 'viz-bar';
                    if (!isVertical) {
                        bar.style.height = `${height}px`;
                    }
                    bar.style.width = `${isVertical ? Math.max(minBarWidth, Math.min(maxBarWidth, proportionalWidth)) : columnWidth}px`;
                    bar.style.background = this.getBarGradient(index);

                    const label = document.createElement('span');
                    label.className = 'viz-bar-label';
                    label.textContent = value;
                    bar.appendChild(label);
                    
                    // Apply states
                    if (this.state.active.includes(index)) {
                        bar.classList.add('active');
                    }
                    if (this.state.comparing.includes(index)) {
                        bar.classList.add('comparing');
                    }
                    if (this.state.swapping && (this.state.swapping[0] === index || this.state.swapping[1] === index)) {
                        bar.classList.add('swapping');
                    }
                    const sortedByTail = Number.isInteger(this.state.sortedFrom) && index >= this.state.sortedFrom;
                    const sortedByList = Array.isArray(this.state.sortedIndices) && this.state.sortedIndices.includes(index);
                    if (sortedByTail || sortedByList) {
                        bar.classList.add('sorted');
                    }
                    
                    fragment.appendChild(bar);
                });
                
                this.container.appendChild(fragment);
            }
            
            renderGraph() {
                this.container.style.minHeight = '360px';
                this.container.style.backgroundImage = 'none';
                this.container.style.backgroundColor = 'white';
                this.container.style.alignItems = 'center';
                this.container.style.justifyContent = 'center';
                this.container.style.position = 'relative';
                this.container.style.overflow = 'hidden';

                const width = this.container.clientWidth || 640;
                const height = this.container.clientHeight || 360;
                const padding = 44;
                const xs = this.state.nodes.map(n => n.x);
                const ys = this.state.nodes.map(n => n.y);
                const minX = Math.min(...xs);
                const maxX = Math.max(...xs);
                const minY = Math.min(...ys);
                const maxY = Math.max(...ys);
                const rangeX = Math.max(1, maxX - minX);
                const rangeY = Math.max(1, maxY - minY);
                const scaleX = Math.max(0.25, (width - padding * 2) / rangeX);
                const scaleY = Math.max(0.25, (height - padding * 2) / rangeY);
                const nodePos = {};
                this.state.nodes.forEach(node => {
                    nodePos[node.id] = {
                        x: padding + (node.x - minX) * scaleX,
                        y: padding + (node.y - minY) * scaleY
                    };
                });
                
                // Render edges first (so they are behind nodes)
                this.state.edges.forEach(edge => {
                    const fromNode = this.state.nodes.find(n => n.id === edge.from);
                    const toNode = this.state.nodes.find(n => n.id === edge.to);
                    
                    if (fromNode && toNode) {
                        const edgeEl = document.createElement('div');
                        edgeEl.className = 'graph-edge';
                        
                        // Calculate position and angle
                        const fromPos = nodePos[fromNode.id];
                        const toPos = nodePos[toNode.id];
                        const dx = toPos.x - fromPos.x;
                        const dy = toPos.y - fromPos.y;
                        const length = Math.sqrt(dx * dx + dy * dy);
                        const angle = Math.atan2(dy, dx) * 180 / Math.PI;
                        
                        edgeEl.style.width = `${length - 30}px`;
                        edgeEl.style.left = `${fromPos.x + 15}px`;
                        edgeEl.style.top = `${fromPos.y + 15}px`;
                        edgeEl.style.transform = `rotate(${angle}deg)`;
                        
                        // Highlight active edges for BFS/DFS
                        if (this.state.current && 
                            ((this.algorithm === 'bfs' || this.algorithm === 'dfs') && edge.from === this.state.current) ||
                            (this.algorithm === 'dijkstra' && edge.from === this.state.current)) {
                            edgeEl.classList.add('active');
                        }
                        
                        this.container.appendChild(edgeEl);
                    }
                });
                
                // Render nodes
                this.state.nodes.forEach(node => {
                    const nodeEl = document.createElement('div');
                    const pos = nodePos[node.id];
                    nodeEl.className = 'graph-node';
                    nodeEl.style.left = `${pos.x}px`;
                    nodeEl.style.top = `${pos.y}px`;
                    nodeEl.textContent = node.id;
                    
                    // Apply states
                    if (node.visited) {
                        nodeEl.classList.add('visited');
                    }
                    if (this.state.current === node.id) {
                        nodeEl.classList.add('current');
                    }
                    
                    // Show distance for Dijkstra
                    if (this.algorithm === 'dijkstra' && node.dist < Infinity) {
                        const distEl = document.createElement('div');
                        distEl.className = 'absolute -bottom-6 text-xs font-bold text-slate-600';
                        distEl.textContent = node.dist === 0 ? '0' : node.dist;
                        nodeEl.appendChild(distEl);
                    }
                    
                    this.container.appendChild(nodeEl);
                });
            }

            renderTree() {
                this.container.style.minHeight = '360px';
                this.container.style.backgroundImage = 'none';
                this.container.style.backgroundColor = 'white';
                this.container.style.alignItems = 'center';
                this.container.style.justifyContent = 'center';
                this.container.style.position = 'relative';
                this.container.style.overflow = 'hidden';

                const width = this.container.clientWidth || 640;
                const height = this.container.clientHeight || 360;
                const nodePos = {};

                this.state.nodes.forEach(node => {
                    nodePos[node.id] = {
                        x: node.x * width,
                        y: node.y * height
                    };
                });

                this.state.edges.forEach(edge => {
                    const from = nodePos[edge.from];
                    const to = nodePos[edge.to];
                    if (!from || !to) return;
                    const dx = to.x - from.x;
                    const dy = to.y - from.y;
                    const length = Math.sqrt(dx * dx + dy * dy);
                    const angle = Math.atan2(dy, dx) * 180 / Math.PI;
                    const line = document.createElement('div');
                    line.className = 'tree-edge';
                    line.style.width = `${length}px`;
                    line.style.left = `${from.x}px`;
                    line.style.top = `${from.y}px`;
                    line.style.transform = `rotate(${angle}deg)`;
                    this.container.appendChild(line);
                });

                this.state.nodes.forEach(node => {
                    if (this.algorithm === 'bst' && !node.inserted) return;
                    const el = document.createElement('div');
                    const pos = nodePos[node.id];
                    el.className = 'tree-node';
                    el.style.left = `${pos.x - 30}px`;
                    el.style.top = `${pos.y - 30}px`;
                    el.textContent = node.value;
                    if (node.inserted) el.classList.add('visited');
                    if (this.state.current === node.id) el.classList.add('current');
                    this.container.appendChild(el);
                });
            }

            renderStructure() {
                this.container.style.minHeight = '320px';
                this.container.style.backgroundImage = 'none';
                this.container.style.backgroundColor = 'white';
                this.container.style.alignItems = 'center';
                this.container.style.justifyContent = 'center';
                this.container.style.position = 'relative';
                this.container.style.overflow = 'hidden';

                const wrap = document.createElement('div');
                wrap.className = 'structure-wrap';

                if (this.algorithm === 'stack') {
                    const stack = document.createElement('div');
                    stack.className = 'stack';
                    this.state.items.forEach((item, idx) => {
                        const slot = document.createElement('div');
                        slot.className = 'stack-slot';
                        if (idx === this.state.activeIndex) slot.classList.add('active');
                        slot.textContent = item;
                        stack.appendChild(slot);
                    });
                    wrap.appendChild(stack);
                } else if (this.algorithm === 'queue') {
                    const queue = document.createElement('div');
                    queue.className = 'queue';
                    this.state.items.forEach((item, idx) => {
                        const slot = document.createElement('div');
                        slot.className = 'queue-slot';
                        if (idx === this.state.activeIndex) slot.classList.add('active');
                        slot.textContent = item;
                        queue.appendChild(slot);
                    });
                    wrap.appendChild(queue);
                } else if (this.algorithm === 'hash') {
                    const table = document.createElement('div');
                    table.className = 'hash-table';
                    this.state.buckets.forEach((bucket, idx) => {
                        const row = document.createElement('div');
                        row.className = 'hash-bucket';
                        if (idx === this.state.activeBucket) row.classList.add('active');
                        const label = document.createElement('div');
                        label.className = 'hash-label';
                        label.textContent = idx;
                        row.appendChild(label);
                        bucket.forEach(val => {
                            const item = document.createElement('div');
                            item.className = 'hash-item';
                            item.textContent = val;
                            row.appendChild(item);
                        });
                        table.appendChild(row);
                    });
                    wrap.appendChild(table);
                }

                this.container.appendChild(wrap);
            }
            
            updateStats() {
                document.getElementById('comparisons').textContent = this.stats.comparisons;
                document.getElementById('swaps').textContent = this.stats.swaps;
            }
        }
        
        // ======================
        // INITIALIZATION
        // ======================
        document.addEventListener('DOMContentLoaded', () => {
            // Initialize visualizer
            const visualizer = new AlgorithmVisualizer('visualizationContainer');
            visualizer.setSpeed(800);
            
            // Set initial state for demo
            visualizer.reset('bubble');

            window.addEventListener('resize', () => {
                if (['bfs', 'dfs', 'dijkstra', 'bst', 'avl', 'stack', 'queue', 'hash'].includes(visualizer.algorithm)) {
                    visualizer.render();
                }
            });
            
            // Auto-start demo after 2 seconds for first-time visitors
            setTimeout(() => {
                if (!localStorage.getItem('visualizerDemoShown')) {
                    visualizer.play();
                    localStorage.setItem('visualizerDemoShown', 'true');
                }
            }, 2000);
            
            // Algorithm tab highlighting
            const highlightCodeLine = (algoType, lineNumbers) => {
                document.querySelectorAll(`.algo-card[data-algo-type="${algoType}"] .viz-code-line`).forEach((line, idx) => {
                    line.classList.toggle('active', lineNumbers.includes(idx));
                });
            };
            
            // Demo highlighting for bubble sort
            highlightCodeLine('bubble', [2]);
        });
    </script>
</body>

</html>
