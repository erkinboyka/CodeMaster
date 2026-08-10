<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contest;
use App\Models\ContestProblem;

class ContestProblemsSeeder extends Seeder
{
    public function run(): void
    {
        $contests = [
            ['title' => 'LeetCode Top 50', 'description' => 'Classic LeetCode problems for interview prep', 'difficulty' => 'medium', 'status' => 'active', 'time_limit' => 120],
            ['title' => 'Codeforces Div 2 Training', 'description' => 'Codeforces problems from Div 2 rounds', 'difficulty' => 'medium', 'status' => 'active', 'time_limit' => 150],
            ['title' => 'Algorithm Masters', 'description' => 'Hard problems for advanced coders', 'difficulty' => 'hard', 'status' => 'active', 'time_limit' => 180],
        ];

        $contestModels = [];
        foreach ($contests as $c) {
            $contestModels[] = Contest::create($c);
        }

        $problems = $this->getProblems($contestModels);

        foreach ($problems as $p) {
            ContestProblem::create($p);
        }

        $this->command->info("Created " . count($problems) . " contest problems across 3 contests.");
    }

    private function getProblems(array $contestModels): array
    {
        $lc = $contestModels[0]->id;
        $cf = $contestModels[1]->id;
        $hard = $contestModels[2]->id;

        return [
            // ─── LEETCODE EASY (1-20) ───
            $this->p($lc, 'Two Sum', 'Given an array of integers nums and an integer target, return indices of the two numbers such that they add up to target.', 'easy', 100, '[2,7,11,15]\n9', '[0,1]', '2 <= nums.length <= 10^4', 'def two_sum(nums, target):\n    pass', [['input' => '[[2,7,11,15],9]', 'output' => '[0,1]']], 1),
            $this->p($lc, 'Reverse Integer', 'Given a signed 32-bit integer x, return x with its digits reversed.', 'easy', 100, '123', '321', '-2^31 <= x <= 2^31-1', 'def reverse(x):\n    pass', [['input' => '123', 'output' => '321']], 2),
            $this->p($lc, 'Palindrome Number', 'Determine whether an integer is a palindrome.', 'easy', 100, '121', 'true', '-2^31 <= x <= 2^31-1', 'def is_palindrome(x):\n    pass', [['input' => '121', 'output' => 'true']], 3),
            $this->p($lc, 'Roman to Integer', 'Convert roman numeral string to integer.', 'easy', 100, 'III', '3', '1 <= s.length <= 39', 'def roman_to_int(s):\n    pass', [['input' => 'III', 'output' => '3']], 4),
            $this->p($lc, 'Longest Common Prefix', 'Find the longest common prefix string amongst an array of strings.', 'easy', 100, '["flower","flow","flight"]', '"fl"', '1 <= strs.length <= 200', 'def longest_common_prefix(strs):\n    pass', [['input' => '["flower","flow","flight"]', 'output' => '"fl"']], 5),
            $this->p($lc, 'Valid Parentheses', 'Given a string of parentheses, determine if the input string is valid.', 'easy', 100, '()[]{}', 'true', '1 <= s.length <= 10^4', 'def is_valid(s):\n    pass', [['input' => '"()[]{}"', 'output' => 'true']], 6),
            $this->p($lc, 'Merge Two Sorted Lists', 'Merge two sorted linked lists into one sorted list.', 'easy', 100, '[1,2,4]\n[1,3,4]', '[1,1,2,3,4,4]', 'Both lists are sorted', 'def merge_two_lists(l1, l2):\n    pass', [['input' => '[[1,2,4],[1,3,4]]', 'output' => '[1,1,2,3,4,4]']], 7),
            $this->p($lc, 'Remove Duplicates from Sorted Array', 'Remove duplicates in-place and return the new length.', 'easy', 100, '[1,1,2]', '2', '1 <= nums.length <= 3*10^4', 'def remove_duplicates(nums):\n    pass', [['input' => '[1,1,2]', 'output' => '2']], 8),
            $this->p($lc, 'Best Time to Buy and Sell Stock', 'Find the maximum profit from buying and selling once.', 'easy', 100, '[7,1,5,3,6,4]', '5', '1 <= prices.length <= 10^5', 'def max_profit(prices):\n    pass', [['input' => '[7,1,5,3,6,4]', 'output' => '5']], 9),
            $this->p($lc, 'Maximum Subarray', 'Find the contiguous subarray with the largest sum.', 'easy', 100, '[-2,1,-3,4,-1,2,1,-5,4]', '6', '1 <= nums.length <= 10^5', 'def max_subarray(nums):\n    pass', [['input' => '[-2,1,-3,4,-1,2,1,-5,4]', 'output' => '6']], 10),
            $this->p($lc, 'Contains Duplicate', 'Return true if any value appears at least twice.', 'easy', 100, '[1,2,3,1]', 'true', '1 <= nums.length <= 10^5', 'def contains_duplicate(nums):\n    pass', [['input' => '[1,2,3,1]', 'output' => 'true']], 11),
            $this->p($lc, 'Plus One', 'Increment the large integer represented as an array of digits.', 'easy', 100, '[1,2,3]', '[1,2,4]', '1 <= digits.length <= 100', 'def plus_one(digits):\n    pass', [['input' => '[1,2,3]', 'output' => '[1,2,4]']], 12),
            $this->p($lc, 'Add Binary', 'Given two binary strings, return their sum as a binary string.', 'easy', 100, '"11"\n"1"', '"100"', '1 <= a.length, b.length <= 10^4', 'def add_binary(a, b):\n    pass', [['input' => '["11","1"]', 'output' => '"100"']], 13),
            $this->p($lc, 'Sqrt(x)', 'Return the square root of x rounded down.', 'easy', 100, '8', '2', '0 <= x <= 2^31-1', 'def my_sqrt(x):\n    pass', [['input' => '8', 'output' => '2']], 14),
            $this->p($lc, 'Climbing Stairs', 'You can climb 1 or 2 steps. How many distinct ways to reach the top?', 'easy', 100, '2', '2', '1 <= n <= 45', 'def climb_stairs(n):\n    pass', [['input' => '3', 'output' => '3']], 15),
            $this->p($lc, 'Maximum Depth of Binary Tree', 'Return the maximum depth of a binary tree.', 'easy', 100, '[3,9,20,null,null,15,7]', '3', '0 <= nodes <= 10^4', 'def max_depth(root):\n    pass', [['input' => '[3,9,20,null,null,15,7]', 'output' => '3']], 16),
            $this->p($lc, 'Symmetric Tree', 'Check whether a binary tree is a mirror of itself.', 'easy', 100, '[1,2,2,3,4,4,3]', 'true', '1 <= nodes <= 1000', 'def is_symmetric(root):\n    pass', [['input' => '[1,2,2,3,4,4,3]', 'output' => 'true']], 17),
            $this->p($lc, 'Valid Anagram', 'Return true if t is an anagram of s.', 'easy', 100, '"anagram"\n"nagaram"', 'true', '1 <= s.length, t.length <= 5*10^4', 'def is_anagram(s, t):\n    pass', [['input' => '["anagram","nagaram"]', 'output' => 'true']], 18),
            $this->p($lc, 'Majority Element', 'Find the element that appears more than n/2 times.', 'easy', 100, '[3,2,3]', '3', '1 <= n <= 5*10^4', 'def majority_element(nums):\n    pass', [['input' => '[3,2,3]', 'output' => '3']], 19),
            $this->p($lc, 'Missing Number', 'Find the missing number in range [0,n].', 'easy', 100, '[3,0,1]', '2', '0 <= n <= 10^4', 'def missing_number(nums):\n    pass', [['input' => '[3,0,1]', 'output' => '2']], 20),

            // ─── LEETCODE MEDIUM (21-40) ───
            $this->p($lc, 'Add Two Numbers', 'Add two numbers represented as linked lists.', 'medium', 200, '[2,4,3]\n[5,6,4]', '[7,0,8]', 'Each list has at least 1 node', 'def add_two_numbers(l1, l2):\n    pass', [['input' => '[[2,4,3],[5,6,4]]', 'output' => '[7,0,8]']], 21),
            $this->p($lc, 'Longest Substring Without Repeating Characters', 'Find the length of the longest substring without repeating characters.', 'medium', 200, '"abcabcbb"', '3', '0 <= s.length <= 5*10^4', 'def length_of_longest_substring(s):\n    pass', [['input' => '"abcabcbb"', 'output' => '3']], 22),
            $this->p($lc, 'Container With Most Water', 'Find two lines that form a container holding the most water.', 'medium', 200, '[1,8,6,2,5,4,8,3,7]', '49', '2 <= n <= 10^5', 'def max_area(height):\n    pass', [['input' => '[1,8,6,2,5,4,8,3,7]', 'output' => '49']], 23),
            $this->p($lc, '3Sum', 'Find all unique triplets that sum to zero.', 'medium', 200, '[-1,0,1,2,-1,-4]', '[[-1,-1,2],[-1,0,1]]', '3 <= nums.length <= 3000', 'def three_sum(nums):\n    pass', [['input' => '[-1,0,1,2,-1,-4]', 'output' => '[[-1,-1,2],[-1,0,1]]']], 24),
            $this->p($lc, 'Letter Combinations of a Phone Number', 'Return all possible letter combinations for a phone number.', 'medium', 200, '"23"', '["ad","ae","af","bd","be","bf","cd","ce","cf"]', '0 <= digits.length <= 4', 'def letter_combinations(digits):\n    pass', [['input' => '"23"', 'output' => '9 combinations']], 25),
            $this->p($lc, 'Remove Nth Node From End of List', 'Remove the nth node from the end of the list.', 'medium', 200, '[1,2,3,4,5]\n2', '[1,2,3,5]', '1 <= n <= 30', 'def remove_nth_from_end(head, n):\n    pass', [['input' => '[[1,2,3,4,5],2]', 'output' => '[1,2,3,5]']], 26),
            $this->p($lc, 'Generate Parentheses', 'Generate all combinations of well-formed parentheses.', 'medium', 200, '3', '["((()))","(()())","(())()","()(())","()()()"]', '1 <= n <= 8', 'def generate_parenthesis(n):\n    pass', [['input' => '3', 'output' => '5 combinations']], 27),
            $this->p($lc, 'Search in Rotated Sorted Array', 'Search for a target in a rotated sorted array.', 'medium', 200, '[4,5,6,7,0,1,2]\n0', '4', '1 <= nums.length <= 5000', 'def search(nums, target):\n    pass', [['input' => '[[4,5,6,7,0,1,2],0]', 'output' => '4']], 28),
            $this->p($lc, 'Combination Sum', 'Find all combinations of candidates that sum to target.', 'medium', 200, '[2,3,6,7]\n7', '[[2,2,3],[7]]', '1 <= candidates.length <= 30', 'def combination_sum(candidates, target):\n    pass', [['input' => '[[2,3,6,7],7]', 'output' => '[[2,2,3],[7]]']], 29),
            $this->p($lc, 'Group Anagrams', 'Group strings that are anagrams of each other.', 'medium', 200, '["eat","tea","tan","ate","nat","bat"]', '[["eat","tea","ate"],["tan","nat"],["bat"]]', '1 <= strs.length <= 10^4', 'def group_anagrams(strs):\n    pass', [['input' => '["eat","tea","tan","ate","nat","bat"]', 'output' => '3 groups']], 30),
            $this->p($lc, 'Jump Game', 'Determine if you can reach the last index.', 'medium', 200, '[2,3,1,1,4]', 'true', '1 <= nums.length <= 10^4', 'def can_jump(nums):\n    pass', [['input' => '[2,3,1,1,4]', 'output' => 'true']], 31),
            $this->p($lc, 'Merge Intervals', 'Merge all overlapping intervals.', 'medium', 200, '[[1,3],[2,6],[8,10],[15,18]]', '[[1,6],[8,10],[15,18]]', '1 <= intervals.length <= 10^4', 'def merge(intervals):\n    pass', [['input' => '[[1,3],[2,6],[8,10],[15,18]]', 'output' => '[[1,6],[8,10],[15,18]]']], 32),
            $this->p($lc, 'Unique Paths', 'Find unique paths in a grid from top-left to bottom-right.', 'medium', 200, '3\n7', '28', '1 <= m, n <= 100', 'def unique_paths(m, n):\n    pass', [['input' => '[3,7]', 'output' => '28']], 33),
            $this->p($lc, 'Minimum Path Sum', 'Find path that minimizes the sum of all numbers.', 'medium', 200, '[[1,3,1],[1,5,1],[4,2,1]]', '7', '1 <= m, n <= 200', 'def min_path_sum(grid):\n    pass', [['input' => '[[1,3,1],[1,5,1],[4,2,1]]', 'output' => '7']], 34),
            $this->p($lc, 'Sort Colors', 'Sort an array of 0s, 1s, and 2s in-place.', 'medium', 200, '[2,0,2,1,1,0]', '[0,0,1,1,2,2]', '1 <= n <= 300', 'def sort_colors(nums):\n    pass', [['input' => '[2,0,2,1,1,0]', 'output' => '[0,0,1,1,2,2]']], 35),
            $this->p($lc, 'Word Search', 'Check if a word exists in a grid.', 'medium', 200, 'board + ABCCED', 'true', '1 <= m, n <= 6', 'def exist(board, word):\n    pass', [['input' => 'board with ABCCED', 'output' => 'true']], 36),
            $this->p($lc, 'Decode Ways', 'Count the total number of ways to decode a string of digits.', 'medium', 200, '"226"', '3', '1 <= s.length <= 100', 'def num_decodings(s):\n    pass', [['input' => '"226"', 'output' => '3']], 37),
            $this->p($lc, 'Validate Binary Search Tree', 'Determine if a binary tree is a valid BST.', 'medium', 200, '[2,1,3]', 'true', '1 <= nodes <= 10^4', 'def is_valid_bst(root):\n    pass', [['input' => '[2,1,3]', 'output' => 'true']], 38),
            $this->p($lc, 'Binary Tree Level Order Traversal', 'Return level order traversal of a binary tree.', 'medium', 200, '[3,9,20,null,null,15,7]', '[[3],[9,20],[15,7]]', '0 <= nodes <= 2000', 'def level_order(root):\n    pass', [['input' => '[3,9,20,null,null,15,7]', 'output' => '[[3],[9,20],[15,7]]']], 39),
            $this->p($lc, 'Coin Change', 'Compute the fewest coins needed to make up an amount.', 'medium', 200, '[1,2,5]\n11', '3', '1 <= coins.length <= 12', 'def coin_change(coins, amount):\n    pass', [['input' => '[[1,2,5],11]', 'output' => '3']], 40),

            // ─── LEETCODE HARD (41-50) ───
            $this->p($lc, 'Median of Two Sorted Arrays', 'Find median of two sorted arrays in O(log(m+n)).', 'hard', 300, '[1,3]\n[2]', '2.0', '0 <= m, n <= 1000', 'def find_median_sorted_arrays(a, b):\n    pass', [['input' => '[[1,3],[2]]', 'output' => '2.0']], 41),
            $this->p($lc, 'Regular Expression Matching', 'Implement regex matching with . and *.', 'hard', 300, '"aa"\n"a"', 'false', '1 <= s.length <= 20', 'def is_match(s, p):\n    pass', [['input' => '["aa","a"]', 'output' => 'false']], 42),
            $this->p($lc, 'Trapping Rain Water', 'Compute how much water can be trapped.', 'hard', 300, '[0,1,0,2,1,0,1,3,2,1,2,1]', '6', '1 <= n <= 2*10^4', 'def trap(height):\n    pass', [['input' => '[0,1,0,2,1,0,1,3,2,1,2,1]', 'output' => '6']], 43),
            $this->p($lc, 'Merge k Sorted Lists', 'Merge k sorted linked lists.', 'hard', 300, '[[1,4,5],[1,3,4],[2,6]]', '[1,1,2,3,4,4,5,6]', 'k == lists.length', 'def merge_k_lists(lists):\n    pass', [['input' => '[[1,4,5],[1,3,4],[2,6]]', 'output' => '[1,1,2,3,4,4,5,6]']], 44),
            $this->p($lc, 'Longest Valid Parentheses', 'Find length of longest valid parentheses substring.', 'hard', 300, '")()()())"', '4', '0 <= s.length <= 3*10^4', 'def longest_valid_parentheses(s):\n    pass', [['input' => '")()()())"', 'output' => '4']], 45),
            $this->p($lc, 'Sudoku Solver', 'Solve a Sudoku puzzle by filling empty cells.', 'hard', 300, '9x9 grid', 'solved board', 'board.length == 9', 'def solve_sudoku(board):\n    pass', [['input' => '9x9 board', 'output' => 'true']], 46),
            $this->p($lc, 'First Missing Positive', 'Find smallest missing positive integer in O(n) time O(1) space.', 'hard', 300, '[1,2,0]', '3', '1 <= nums.length <= 5*10^5', 'def first_missing_positive(nums):\n    pass', [['input' => '[1,2,0]', 'output' => '3']], 47),
            $this->p($lc, 'Word Ladder', 'Find shortest transformation sequence length.', 'hard', 300, '"hit"\n"cog"', '5', '1 <= beginWord.length <= 10', 'def ladder_length(bw, ew, wl):\n    pass', [['input' => '["hit","cog"]', 'output' => '5']], 48),
            $this->p($lc, 'N-Queens', 'Place n queens on nxn board. Return all solutions.', 'hard', 300, '4', '2 solutions', '1 <= n <= 9', 'def solve_n_queens(n):\n    pass', [['input' => '4', 'output' => '2 solutions']], 49),
            $this->p($lc, 'Minimum Window Substring', 'Find minimum window substring containing all chars of t.', 'hard', 300, '"ADOBECODEBANC"\n"ABC"', '"BANC"', '1 <= m, n <= 10^5', 'def min_window(s, t):\n    pass', [['input' => '["ADOBECODEBANC","ABC"]', 'output' => '"BANC"']], 50),

            // ─── CODEFORCES EASY (51-80) ───
            $this->p($cf, 'Watermelon (CF 4A)', 'Can weight w be divided into two even parts?', 'easy', 100, '8', 'YES', '2 <= w <= 100', 'def watermelon(w):\n    pass', [['input' => '8', 'output' => 'YES']], 51),
            $this->p($cf, 'Team (CF 231A)', 'Count problems where at least 2 of 3 are sure.', 'easy', 100, '3\n1 1 0\n1 1 1\n1 0 0', '2', '1 <= n <= 1000', 'def team(problems):\n    pass', [['input' => '3', 'output' => '2']], 52),
            $this->p($cf, 'Bit++ (CF 282A)', 'Find final value of X after sequence of operations.', 'easy', 100, '6\nX++\n++X\nX++\n++X\nX--\nX--', '1', '1 <= n <= 150', 'def bit_plus_plus(ops):\n    pass', [['input' => '6', 'output' => '1']], 53),
            $this->p($cf, 'Way Too Long Words (CF 71A)', 'Abbreviate words longer than 10 characters.', 'easy', 100, '3\nword\nlocalization\ninternationalization', 'word\nl10n\ni18n', '1 <= n <= 100', 'def too_long_words(words):\n    pass', [['input' => '3', 'output' => 'word l10n i18n']], 54),
            $this->p($cf, 'Beautiful Year (CF 271A)', 'Find smallest year with all distinct digits.', 'easy', 100, '1987', '2013', '1000 <= year <= 9000', 'def beautiful_year(year):\n    pass', [['input' => '1987', 'output' => '2013']], 55),
            $this->p($cf, 'In Search of Easy Problem (CF 1030A)', 'Print HARD if any expert answered 1, else EASY.', 'easy', 100, '3\n0 1 0', 'HARD', '1 <= n <= 100', 'def easy_or_hard(answers):\n    pass', [['input' => '0 1 0', 'output' => 'HARD']], 56),
            $this->p($cf, 'Petya and Strings (CF 112A)', 'Compare two strings lexicographically.', 'easy', 100, 'aaaa\naaaA', '=', '1 <= length <= 100', 'def petya_strings(s1, s2):\n    pass', [['input' => 'aaaa aaaA', 'output' => '=']], 57),
            $this->p($cf, 'Next Round (CF 158A)', 'Count participants who get a prize.', 'easy', 100, '8 5\n10 9 8 7 7 7 5 5', '4', '1 <= n <= 50', 'def next_round(scores, k):\n    pass', [['input' => '8 5', 'output' => '4']], 58),
            $this->p($cf, 'Football (CF 96A)', 'Find if there are 7 consecutive ones.', 'easy', 100, '000000000011111111111111111111', 'NO', 'String length is 7', 'def football(positions):\n    pass', [['input' => '000000000011111111111111111111', 'output' => 'NO']], 59),
            $this->p($cf, 'Young Physicist (CF 69A)', 'Determine if sum of all forces is zero.', 'easy', 100, '3\n1 2 3\n-1 -2 -3\n1 0 0', 'YES', '1 <= n <= 100', 'def physicist(forces):\n    pass', [['input' => '3', 'output' => 'YES']], 60),
            $this->p($cf, 'Chat Room (CF 58A)', 'Check if string can form word hello as subsequence.', 'easy', 100, 'ahhelllllooo', 'YES', '1 <= length <= 100', 'def chat_room(s):\n    pass', [['input' => 'ahhelllllooo', 'output' => 'YES']], 61),
            $this->p($cf, 'Borze (CF 32B)', 'Decode Borze code.', 'easy', 100, '-.-.', '12', '1 <= length <= 200', 'def borze(code):\n    pass', [['input' => '-.-.', 'output' => '12']], 62),
            $this->p($cf, 'Boys and Girls (CF 253A)', 'Place boys and girls to maximize adjacent different-gender pairs.', 'easy', 100, '3 2', 'BGBGB', '0 <= n, m <= 100', 'def boys_and_girls(n, m):\n    pass', [['input' => '3 2', 'output' => 'BGBGB']], 63),
            $this->p($cf, 'Magnets (CF 344A)', 'Count minimum groups of magnets.', 'easy', 100, '3\n10\n10\n10', '3', '1 <= n <= 100', 'def magnets(mags):\n    pass', [['input' => '3', 'output' => '3']], 64),
            $this->p($cf, 'Twins (CF 166A)', 'Find minimum medals to give so at least half of winners have average.', 'easy', 100, '5\n1 2 3 2 1', '4', '1 <= n <= 50', 'def twins(ratings):\n    pass', [['input' => '5', 'output' => '4']], 65),
            $this->p($cf, 'String Task (CF 118A)', 'Convert string: lowercase, remove vowels, insert dot before consonants.', 'easy', 100, 'tour', '.t.r', '1 <= length <= 100', 'def string_task(s):\n    pass', [['input' => 'tour', 'output' => '.t.r']], 66),
            $this->p($cf, 'Soft Drinking (CF 151A)', 'Calculate total toasts from bottles.', 'easy', 100, '15 2 3 4 5 6', '8', '1 <= values <= 1000', 'def soft_drinking(n, k, l, c, p):\n    pass', [['input' => '15 2 3 4 5 6', 'output' => '8']], 67),
            $this->p($cf, 'Amusing Joke (CF 141A)', 'Check if guest name can be rearranged from host name and gift name.', 'easy', 100, 'SANTACLAUS\nDEDMOROZ\nSANTAMOROZDEDCLAUS', 'YES', '1 <= length <= 100', 'def amusing_joke(host, gift, guest):\n    pass', [['input' => 'SANTACLAUS DEDMOROZ', 'output' => 'YES']], 68),
            $this->p($cf, 'Present from Lena (CF 118B)', 'Draw a diamond-shaped pattern of numbers.', 'easy', 100, '2', 'pattern', '0 <= n <= 9', 'def present_from_lena(n):\n    pass', [['input' => '2', 'output' => 'diamond pattern']], 69),
            $this->p($cf, 'Cupcakes (CF 677A)', 'Find minimum cupcakes for n friends.', 'easy', 100, '3', '2', '1 <= n <= 100', 'def cupcakes(n):\n    pass', [['input' => '3', 'output' => '2']], 70),
            $this->p($cf, 'Ilya and Bank Account (CF 313A)', 'Remove one digit to get maximum value.', 'easy', 100, '1203', '123', '-10^9 <= n <= 10^9', 'def bank_account(n):\n    pass', [['input' => '1203', 'output' => '123']], 71),
            $this->p($cf, 'Restoring Three Numbers (CF 1399A)', 'Find three original numbers from sum and pairs.', 'easy', 100, '1 2 3 6', '1 2 3', '1 <= values <= 1000', 'def restoring_three(numbers):\n    pass', [['input' => '1 2 3 6', 'output' => '1 2 3']], 72),
            $this->p($cf, 'Minimum Round (CF 1791A)', 'Find minimum round to complete game.', 'easy', 100, '5\n5 4 3 2 1', '3', '1 <= n <= 100', 'def minimum_round(monsters):\n    pass', [['input' => '5', 'output' => '3']], 73),
            $this->p($cf, 'Elections (CF 1154A)', 'Find votes each candidate received.', 'easy', 100, '15 14 12 15', '15 14 12', '1 <= values <= 100', 'def elections(votes):\n    pass', [['input' => '15 14 12 15', 'output' => '15 14 12']], 74),
            $this->p($cf, 'Lucky Division (CF 122A)', 'Check if number is divisible by any lucky number.', 'easy', 100, '16', 'YES', '1 <= n <= 1000', 'def lucky_division(n):\n    pass', [['input' => '16', 'output' => 'YES']], 75),
            $this->p($cf, 'Arrival of the General (CF 144A)', 'Find minimum swaps to put tallest at front.', 'easy', 100, '4\n3 1 4 2', '2', '2 <= n <= 100', 'def arrival_of_general(heights):\n    pass', [['input' => '4', 'output' => '2']], 76),
            $this->p($cf, 'Insomnia Cure (CF 148A)', 'Count positions not attacked by dragons.', 'easy', 100, '20\n2\n3\n5', '7', '1 <= n, k <= 100', 'def insomnia_cure(n, dragons):\n    pass', [['input' => '20', 'output' => '7']], 77),
            $this->p($cf, 'I love percent username (CF 155A)', 'Count personal best achievements.', 'easy', 100, '6\n3 1 4 1 5 9', '2', '1 <= n <= 1000', 'def love_username(scores):\n    pass', [['input' => '6', 'output' => '2']], 78),
            $this->p($cf, 'Little Elephant and Function (CF 205A)', 'Check if permutation can be sorted by swapping adjacent.', 'easy', 100, '4\n2 1 4 3', 'YES', '1 <= n <= 100', 'def elephant_function(permutation):\n    pass', [['input' => '4', 'output' => 'YES']], 79),
            $this->p($cf, 'Helpful Maths (CF 339A)', 'Sort numbers in expression of 1s, 2s, 3s.', 'easy', 100, '3+1+2', '1+2+3', 'Only 1,2,3,+', 'def helpful_maths(expr):\n    pass', [['input' => '3+1+2', 'output' => '1+2+3']], 80),

            // ─── CODEFORCES MEDIUM (81-95) ───
            $this->p($cf, 'Xenia and Ringroad (CF 339B)', 'Calculate total distance for deliveries on a ringroad.', 'medium', 200, '3 3\n1 3 2', '6', '1 <= n, m <= 10^5', 'def ringroad(n, tasks):\n    pass', [['input' => '3 3', 'output' => '6']], 81),
            $this->p($cf, 'Laptops (CF 456A)', 'Find if there exists a pair where one dominates.', 'medium', 200, '2\n1 2\n2 1', 'Happy Alex', '2 <= n <= 100', 'def laptops(pairs):\n    pass', [['input' => '2', 'output' => 'Happy Alex']], 82),
            $this->p($cf, 'Chores (CF 339A)', 'Find minimum total time with parallel tasks.', 'medium', 200, '5 2\n1 2 3 4 5', '6', '1 <= n, k <= 100', 'def chores(n, k, times):\n    pass', [['input' => '5 2', 'output' => '6']], 83),
            $this->p($cf, 'Dreamoon and WiFi (CF 476A)', 'Find probability of reaching the target.', 'medium', 200, '++-+-\n+-', '0.5', '1 <= length <= 10000', 'def dreamoon_wifi(intended, actual):\n    pass', [['input' => '++-+- +-', 'output' => '0.5']], 84),
            $this->p($cf, 'Two Buttons (CF 520B)', 'Find minimum clicks to go from n to m.', 'medium', 200, '4 7', '3', '1 <= n, m <= 10^4', 'def two_buttons(n, m):\n    pass', [['input' => '4 7', 'output' => '3']], 85),
            $this->p($cf, 'Queue at School (CF 266B)', 'Simulate queue swaps over t seconds.', 'medium', 200, '5 1\nBGGBG', 'GBGGB', '1 <= n, t <= 50', 'def queue_at_school(n, t, queue):\n    pass', [['input' => '5 1 BGGBG', 'output' => 'GBGGB']], 86),
            $this->p($cf, 'Kefa and First Steps (CF 580A)', 'Find longest non-decreasing subarray.', 'medium', 200, '6\n2 2 1 3 4 1', '3', '1 <= n <= 10^5', 'def kefa_first_steps(arr):\n    pass', [['input' => '6', 'output' => '3']], 87),
            $this->p($cf, 'Sereja and Dima (CF 381A)', 'Find scores of both players with optimal play.', 'medium', 200, '4\n1 2 3 4', '6 4', '1 <= n <= 1000', 'def sereja_dima(cards):\n    pass', [['input' => '4', 'output' => '6 4']], 88),
            $this->p($cf, 'Petya and Staircases (CF 119A)', 'Check if Petya can reach the top avoiding dirty steps.', 'medium', 200, '10 6\n2 4 5 6 8 10', 'NO', '1 <= n <= 300', 'def petya_stairs(n, dirty):\n    pass', [['input' => '10 6', 'output' => 'NO']], 89),
            $this->p($cf, 'Diverse Substring (CF 1358A)', 'Find a diverse substring with equal 0s and 1s.', 'medium', 200, '10', '01', '1 <= n <= 100', 'def diverse_substring(s):\n    pass', [['input' => '10', 'output' => '01']], 90),
            $this->p($cf, 'Team Olympiad (CF 490A)', 'Form teams of 3 with different skills.', 'medium', 200, '7\n1 3 2 3 2 3 3', '2 teams', '1 <= n <= 5000', 'def team_olympiad(skills):\n    pass', [['input' => '7', 'output' => '2']], 91),
            $this->p($cf, 'Little Pony and Sort by Shift (CF 454A)', 'Find minimum shift-right operations to sort.', 'medium', 200, '4\n1 3 5 2', '3', '1 <= n <= 10^5', 'def sort_by_shift(arr):\n    pass', [['input' => '4', 'output' => '3']], 92),
            $this->p($cf, 'Ilovlav and Bank Account (CF 313A)', 'Remove one digit to get max value.', 'medium', 200, '-10', '0', '-10^9 <= n <= 10^9', 'def bank_account(n):\n    pass', [['input' => '-10', 'output' => '0']], 93),
            $this->p($cf, 'Cupcakes Variation (CF 677B)', 'Calculate total cupcakes needed.', 'medium', 200, '5 2', '3', '1 <= n, h, k <= 100', 'def cupcakes_b(n, h, k):\n    pass', [['input' => '5 2', 'output' => '3']], 94),
            $this->p($cf, 'Petya and Countryside (CF 66B)', 'Find the largest plateau of consecutive heights.', 'medium', 200, '5\n1 2 3 2 1', '5', '1 <= n <= 1000', 'def countryside(heights):\n    pass', [['input' => '5', 'output' => '5']], 95),

            // ─── CODEFORCES HARD (96-100) ───
            $this->p($hard, 'Xenia and Tree (CF 342E)', 'Process queries on a tree: paint node or find distance to nearest red.', 'hard', 300, '4 6\n1 2\n2 3\n3 4', 'queries output', '1 <= n, m <= 10^5', 'def xenia_and_tree(n, edges, queries):\n    pass', [['input' => '4 6', 'output' => 'queries']], 96),
            $this->p($hard, 'Jeff and Removing Periods (CF 351D)', 'Find minimum operations to sort a permutation.', 'hard', 300, '3\n3 2 1', '2', '1 <= n <= 10^5', 'def jeff_removing(periods):\n    pass', [['input' => '3', 'output' => '2']], 97),
            $this->p($hard, 'Lomsat Gelral (CF 1208E)', 'For each subarray of length k, find most frequent element and sum.', 'hard', 300, '7 3\n1 2 1 3 1 2 1', '2 3 4 4 3', '1 <= n <= 10^5', 'def lomsat_gelral(arr, k):\n    pass', [['input' => '7 3', 'output' => '2 3 4 4 3']], 98),
            $this->p($hard, 'Furukawa Nagisa and Tree (CF 576E)', 'Process queries on tree: change edge weight or count paths with XOR k.', 'hard', 300, '3 3\n1 2 1\n2 3 2', '1 1 1', '1 <= n, q <= 50000', 'def furukawa_tree(n, edges, queries):\n    pass', [['input' => '3 3', 'output' => '1 1 1']], 99),
            $this->p($hard, 'XOR and Favorite Number (CF 241E)', 'Count pairs with XOR equal to favorite number.', 'hard', 300, '5 2\n1 2 3 4 5', '3', '1 <= n <= 10^5', 'def xor_favorite(arr, k):\n    pass', [['input' => '5 2', 'output' => '3']], 100),
        ];
    }

    private function p(int $contestId, string $title, string $desc, string $diff, int $pts, string $input, string $output, string $constraints, string $code, array $tests, int $order): array
    {
        return [
            'contest_id' => $contestId,
            'title' => $title,
            'description' => $desc,
            'difficulty' => $diff,
            'points' => $pts,
            'input_example' => $input,
            'output_example' => $output,
            'constraints' => $constraints,
            'starter_code' => $code,
            'language' => 'python',
            'tests_json' => $tests,
            'time_limit' => 2,
            'memory_limit' => 256,
            'order_num' => $order,
        ];
    }
}
