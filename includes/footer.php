<?php
$footerVariant = $footerVariant ?? 'default';
$footerShowBinary = $footerShowBinary ?? true;
$footerTranslate = static function (string $key): string {
	if (function_exists('t')) {
		return t($key);
	}
	return $key;
};
$footerMadeWithLove = $footerMadeWithLove ?? $footerTranslate('footer_made_with_love');

if ($footerVariant === 'home') {
	$footerHomeTr = $footerTr ?? $footerTranslate;
	$footerRightsKey = $footerRightsKey ?? 'dashboard_rights';
	?>
	<footer class="bg-gray-900 text-gray-300 py-12 relative overflow-hidden">
		<div class="absolute inset-0 opacity-5">
			<div class="absolute top-1/4 left-1/4 w-96 h-96 bg-brand-500 rounded-full blur-3xl"></div>
			<div class="absolute bottom-1/3 right-1/3 w-96 h-96 bg-indigo-600 rounded-full blur-3xl"></div>
		</div>
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
			<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
				<div class="md:col-span-1">
					<div class="flex items-center gap-3 mb-4">
						<div
							class="w-12 h-12 rounded-xl bg-gradient-to-r from-brand-600 to-indigo-500 text-white flex items-center justify-center relative overflow-hidden shadow-lg">
							<i class="fas fa-graduation-cap text-xl relative z-10"></i>
							<div
								class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(255,255,255,0.3),transparent_70%)]">
							</div>
						</div>
						<div class="font-bold text-xl text-white">CodeMaster</div>
					</div>
					<p class="text-gray-400 mb-4"><?= htmlspecialchars($footerHomeTr('desc')) ?></p>
					<div class="flex gap-3">
						<a href="#"
							class="w-11 h-11 rounded-full bg-gray-800 flex items-center justify-center hover:bg-brand-600 transition-colors text-xl">
							<i class="fab fa-telegram-plane"></i>
						</a>
						<a href="#"
							class="w-11 h-11 rounded-full bg-gray-800 flex items-center justify-center hover:bg-blue-600 transition-colors text-xl">
							<i class="fab fa-vk"></i>
						</a>
						<a href="#"
							class="w-11 h-11 rounded-full bg-gray-800 flex items-center justify-center hover:bg-green-600 transition-colors text-xl">
							<i class="fab fa-whatsapp"></i>
						</a>
						<a href="#"
							class="w-11 h-11 rounded-full bg-gray-800 flex items-center justify-center hover:bg-red-600 transition-colors text-xl">
							<i class="fab fa-youtube"></i>
						</a>
					</div>
				</div>
				<div class="md:col-span-1">
					<h3 class="text-white font-bold text-lg mb-4 flex items-center">
						<i class="fas fa-sitemap text-brand-500 mr-2"></i>
						Навигация
					</h3>
					<ul class="space-y-3">
						<li><a href="?action=courses" class="hover:text-white transition-colors flex items-center"><i
									class="fas fa-chevron-right text-brand-500 mr-2 text-xs"></i><?= htmlspecialchars($footerHomeTr('courses')) ?></a>
						</li>
						<li><a href="?action=vacancies" class="hover:text-white transition-colors flex items-center"><i
									class="fas fa-chevron-right text-brand-500 mr-2 text-xs"></i><?= htmlspecialchars($footerHomeTr('vacancies')) ?></a>
						</li>
						<li><a href="?action=ratings" class="hover:text-white transition-colors flex items-center"><i
									class="fas fa-chevron-right text-brand-500 mr-2 text-xs"></i><?= htmlspecialchars($footerHomeTr('reviews')) ?></a>
						</li>
						<li><a href="?action=login" class="hover:text-white transition-colors flex items-center"><i
									class="fas fa-chevron-right text-brand-500 mr-2 text-xs"></i><?= htmlspecialchars($footerHomeTr('login')) ?></a>
						</li>
						<li><a href="#events" class="hover:text-white transition-colors flex items-center"><i
									class="fas fa-chevron-right text-brand-500 mr-2 text-xs"></i><?= htmlspecialchars($footerHomeTr('events_nav')) ?></a>
						</li>
					</ul>
				</div>
			</div>
			<div class="border-t border-gray-800 mt-10 pt-8 text-center text-gray-500 text-sm">
				<?= $footerTranslate($footerRightsKey) ?>
				<p class="text-xs opacity-75"><?= htmlspecialchars($footerMadeWithLove) ?></p>
			</div>
			<?php if ($footerShowBinary): ?>
				<div class="mt-8 opacity-10 code-font text-xs select-none whitespace-nowrap overflow-hidden">
					<div class="animate-[codeFlow_80s_linear_infinite]">
						01000110 01110010 01100101 01100101 00100000 01001001 01010100 00100000 01100101 01100100 01110101
						01100011 01100001 01110100 01101001 01101111 01101110 00100000 01100110 01101111 01110010 00100000
						01100101 01110110 01100101 01110010 01111001 01101111 01101110 01100101
					</div>
				</div>
			<?php endif; ?>
		</div>
	</footer>
	<?php
	return;
}

$footerContext = $footerContext ?? 'dashboard';
$footerConfigs = [
	'courses' => [
		'desc_key' => 'courses_footer_desc',
		'columns' => [
			[
				'title_key' => 'courses_nav',
				'links' => [
					['?action=courses', 'courses_nav_courses'],
					['?action=vacancies', 'courses_nav_vacancies'],
					['?action=blog', 'courses_nav_blog']
				]
			],
			[
				'title_key' => 'courses_company',
				'links' => [
					['?action=about', 'courses_about'],
					['?action=contacts', 'courses_contacts'],
					['?action=partners', 'courses_career']
				]
			],
			[
				'title_key' => 'courses_resources',
				'links' => [
					['?action=support', 'courses_support_center'],
					['?action=docs', 'courses_docs'],
					['?action=community', 'courses_community']
				]
			]
		],
		'legal_links' => [
			['?action=privacy', 'courses_privacy'],
			['?action=terms', 'courses_terms']
		],
		'rights_key' => 'courses_rights'
	],
	'dashboard' => [
		'desc_key' => 'dashboard_footer_desc',
		'columns' => [
			[
				'title_key' => 'dashboard_resources',
				'links' => [
					['?action=courses', 'dashboard_nav_courses'],
					['?action=vacancies', 'dashboard_nav_vacancies'],
					['?action=blog', 'dashboard_nav_blog']
				]
			],
			[
				'title_key' => 'dashboard_company',
				'links' => [
					['?action=about', 'dashboard_about'],
					['?action=contacts', 'dashboard_contacts'],
					['?action=partners', 'dashboard_partners']
				]
			],
			[
				'title_key' => 'dashboard_support',
				'links' => [
					['?action=support', 'dashboard_support_center'],
					['?action=docs', 'dashboard_docs'],
					['?action=charity', 'dashboard_charity']
				]
			]
		],
		'legal_links' => [
			['?action=terms', 'dashboard_terms'],
			['?action=privacy', 'dashboard_privacy']
		],
		'rights_key' => 'dashboard_rights'
	],
	'profile' => [
		'desc_key' => 'profile_footer_desc',
		'columns' => [
			[
				'title_key' => 'profile_resources',
				'links' => [
					['?action=courses', 'profile_nav_courses'],
					['?action=vacancies', 'profile_nav_vacancies'],
					['?action=blog', 'profile_nav_blog']
				]
			],
			[
				'title_key' => 'profile_company',
				'links' => [
					['?action=about', 'profile_about_company'],
					['?action=contacts', 'profile_contacts'],
					['?action=partners', 'profile_partners']
				]
			],
			[
				'title_key' => 'profile_support',
				'links' => [
					['?action=support', 'profile_support_center'],
					['?action=docs', 'profile_docs'],
					['?action=charity', 'profile_charity']
				]
			]
		],
		'legal_links' => [
			['?action=terms', 'profile_terms'],
			['?action=privacy', 'profile_privacy']
		],
		'rights_key' => 'profile_rights'
	],
	'ratings' => [
		'desc_key' => 'ratings_footer_desc',
		'columns' => [
			[
				'title_key' => 'ratings_resources',
				'links' => [
					['?action=courses', 'ratings_nav_courses'],
					['?action=vacancies', 'ratings_nav_vacancies'],
					['?action=blog', 'ratings_nav_blog']
				]
			],
			[
				'title_key' => 'ratings_company',
				'links' => [
					['?action=about', 'ratings_about'],
					['?action=contacts', 'ratings_contacts'],
					['?action=partners', 'ratings_partners']
				]
			],
			[
				'title_key' => 'ratings_support',
				'links' => [
					['?action=support', 'ratings_support_center'],
					['?action=docs', 'ratings_docs'],
					['?action=charity', 'ratings_charity']
				]
			]
		],
		'legal_links' => [
			['?action=terms', 'ratings_terms'],
			['?action=privacy', 'ratings_privacy']
		],
		'rights_key' => 'ratings_rights'
	],
	'vacancies' => [
		'desc_key' => 'vacancies_footer_desc',
		'columns' => [
			[
				'title_key' => 'vacancies_resources',
				'links' => [
					['?action=courses', 'vacancies_nav_courses'],
					['?action=vacancies', 'vacancies_nav_vacancies'],
					['?action=blog', 'vacancies_nav_blog']
				]
			],
			[
				'title_key' => 'vacancies_company',
				'links' => [
					['?action=about', 'vacancies_about'],
					['?action=contacts', 'vacancies_contacts'],
					['?action=partners', 'vacancies_partners']
				]
			],
			[
				'title_key' => 'vacancies_support',
				'links' => [
					['?action=support', 'vacancies_support_center'],
					['?action=docs', 'vacancies_docs'],
					['?action=charity', 'vacancies_charity']
				]
			]
		],
		'legal_links' => [
			['?action=terms', 'vacancies_terms'],
			['?action=privacy', 'vacancies_privacy']
		],
		'rights_key' => 'vacancies_rights'
	]
];
$footerConfig = $footerConfigs[$footerContext] ?? $footerConfigs['dashboard'];
$footerExtraTags = $footerExtraTags ?? [];
$footerLegalLinks = $footerLegalLinks ?? ($footerConfig['legal_links'] ?? []);
$footerRightsText = $footerRightsText ?? $footerTranslate($footerConfig['rights_key']);
?>
<footer class="bg-white border-t border-gray-200 mt-12">
	<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
		<div class="grid grid-cols-1 md:grid-cols-4 gap-8">
			<div class="md:col-span-1">
				<div class="flex">
					<span class="text-2xl font-bold text-indigo-600">CodeMaster</span>
				</div>
				<p class="mt-4 text-gray-500 text-sm">
					<?= $footerTranslate($footerConfig['desc_key']) ?>
				</p>
				<div class="mt-4 flex space-x-6">
					<a href="#" class="text-gray-400 hover:text-gray-500">
						<span class="sr-only">Facebook</span>
						<i class="fab fa-facebook-f text-lg"></i>
					</a>
					<a href="#" class="text-gray-400 hover:text-gray-500">
						<span class="sr-only">Instagram</span>
						<i class="fab fa-instagram text-lg"></i>
					</a>
					<a href="#" class="text-gray-400 hover:text-gray-500">
						<span class="sr-only">Twitter</span>
						<i class="fab fa-twitter text-lg"></i>
					</a>
					<a href="#" class="text-gray-400 hover:text-gray-500">
						<span class="sr-only">GitHub</span>
						<i class="fab fa-github text-lg"></i>
					</a>
				</div>
			</div>
			<?php foreach ($footerConfig['columns'] as $column): ?>
				<div class="md:col-span-1">
					<h3 class="text-sm font-semibold text-gray-400 tracking-wider uppercase">
						<?= $footerTranslate($column['title_key']) ?>
					</h3>
					<ul class="mt-4 space-y-3">
						<?php foreach ($column['links'] as $link): ?>
							<li><a href="<?= htmlspecialchars($link[0]) ?>"
									class="text-base text-gray-500 hover:text-indigo-600 transition"><?= $footerTranslate($link[1]) ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
		<?php if (!empty($footerLegalLinks)): ?>
				<div class="mt-8 border-t border-gray-200 pt-8 flex justify-center md:justify-end">
					<div class="flex flex-wrap justify-center gap-x-6 gap-y-2 md:justify-end text-center md:text-right">
						<?php foreach ($footerLegalLinks as $legal): ?>
							<a href="<?= htmlspecialchars($legal[0]) ?>"
								class="text-gray-400 hover:text-indigo-600"><?= $footerTranslate($legal[1]) ?></a>
						<?php endforeach; ?>
					</div>
			</div>
		<?php endif; ?>
		<div class="border-t border-gray-800 mt-10 pt-8 text-center text-gray-500 text-sm">
			<?= $footerRightsText ?>
			<p class="text-xs opacity-75"><?= htmlspecialchars($footerMadeWithLove) ?></p>
			<?php if (!empty($footerExtraTags)): ?>
				<div class="mt-4 flex flex-wrap justify-center gap-3 text-xs px-4 text-center">
					<?php foreach ($footerExtraTags as $index => $tag): ?>
						<?php if ($index > 0): ?>
							<span>•</span>
						<?php endif; ?>
						<span><?= htmlspecialchars($tag) ?></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php if ($footerShowBinary): ?>
			<div class="mt-8 opacity-10 code-font text-xs select-none whitespace-nowrap overflow-hidden">
				<div class="animate-[codeFlow_80s_linear_infinite]">
					01000110 01110010 01100101 01100101 00100000 01001001 01010100 00100000 01100101 01100100 01110101
					01100011 01100001 01110100 01101001 01101111 01101110 00100000 01100110 01101111 01110010 00100000
					01100101 01110110 01100101 01110010 01111001 01101111 01101110 01100101
				</div>
			</div>
		<?php endif; ?>
	</div>
</footer>

<?php include __DIR__ . '/ai_tutor_modal.php'; ?>

