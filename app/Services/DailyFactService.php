<?php

namespace App\Services;

class DailyFactService
{
    private array $facts = [
        // PHP / Laravel — things that make you sharper at work
        ['category' => 'PHP', 'text' => 'array_map creates a new array. array_walk modifies in place. array_filter removes. If you need to transform + filter, chain array_filter(array_map(...)) — not a foreach. Reads cleaner, profiles the same.'],
        ['category' => 'Laravel', 'text' => 'Eager loading (with()) runs 2 queries. Lazy loading inside a loop runs N+1. On a list of 100 orders with users: 2 queries vs 101. Always check: "Am I inside a loop? Then I need with()."'],
        ['category' => 'PHP', 'text' => 'PHP passes objects by reference-handle, not by value. $a = $b doesn\'t clone — both point to the same object. Modify $a, $b changes too. Use clone $obj when you need independence.'],
        ['category' => 'Laravel', 'text' => 'DB::transaction() auto-retries on deadlock in Laravel 11+. But if your closure has side effects (sending email, API calls), those fire again on retry. Keep transactions pure — only DB work inside.'],
        ['category' => 'SQL', 'text' => 'EXPLAIN shows "Using filesort" → MySQL can\'t use an index for ORDER BY. Fix: add a composite index covering both WHERE and ORDER BY columns in that exact order. WHERE first, ORDER BY second.'],
        ['category' => 'PHP', 'text' => 'readonly properties (PHP 8.2) can only be set once — in the constructor. After that, immutable. Use them for value objects and DTOs. No more accidentally mutating a price or date after creation.'],

        // SQL / Database — things that save you in production
        ['category' => 'SQL', 'text' => 'A covering index includes all columns the query needs. MySQL reads only the index, never touches the table. If your query is SELECT name FROM users WHERE email = ?, index on (email, name) is covering.'],
        ['category' => 'SQL', 'text' => 'COUNT(*) counts rows. COUNT(column) counts non-NULL values in that column. COUNT(DISTINCT column) counts unique non-NULL values. Using the wrong one silently gives wrong numbers.'],
        ['category' => 'Databases', 'text' => 'A deadlock means two transactions each hold a lock the other needs. MySQL picks one victim and rolls it back. Prevention: always acquire locks in the same order across all code paths.'],
        ['category' => 'SQL', 'text' => 'WHERE column IS NOT NULL can use an index. WHERE column != "value" usually can\'t (it matches too many rows). Rewrite negative conditions as positive ones when possible.'],
        ['category' => 'Databases', 'text' => 'INSERTing 1000 rows one-by-one = 1000 round trips. INSERT ... VALUES (...), (...), (...) in one query = 1 round trip. Batch inserts are 10-50x faster. Laravel: use insert() with an array of arrays.'],
        ['category' => 'SQL', 'text' => 'LEFT JOIN keeps all rows from the left table even if no match exists on the right (fills NULLs). INNER JOIN drops unmatched rows. Wrong join type = silently missing data in reports.'],

        // Architecture / Design — things that make you senior
        ['category' => 'Architecture', 'text' => 'A service class should do one coordination job. If it has 20 methods, it\'s a god class. Split by use case: OrderCreationService, OrderCancellationService. Each with 1-3 public methods max.'],
        ['category' => 'Architecture', 'text' => 'Never catch Exception and swallow it silently. Either handle it (retry, fallback, user message) or let it bubble. Silent catches create bugs that take days to find because nothing in the logs points to them.'],
        ['category' => 'Architecture', 'text' => 'Feature flags > feature branches for long-lived work. Ship dark code behind a flag, enable for 1% of users, watch metrics, roll forward or kill. Merge daily, deploy daily, release when ready.'],
        ['category' => 'Architecture', 'text' => 'The repository pattern shines when you have complex queries reused across multiple services. If every query is unique and simple, Eloquent directly is fine. Don\'t add abstraction layers you don\'t need yet.'],
        ['category' => 'Design', 'text' => 'Naming is design. A method called processData() tells you nothing. A method called calculateMonthlyRevenue() tells you everything. If you can\'t name it clearly, you don\'t understand what it does yet.'],
        ['category' => 'Design', 'text' => 'Composition > inheritance for behavior reuse. Traits in PHP are composition. A class can use multiple traits but extend only one parent. When you reach for "extends", ask: "Is this really an is-a relationship?"'],

        // Performance — things that save production
        ['category' => 'Performance', 'text' => 'Redis GET is ~0.1ms. MySQL SELECT by primary key is ~0.5ms. HTTP call to external API is ~100-500ms. Cache what you call often and changes rarely. A 5-minute TTL eliminates 99.7% of repeated reads.'],
        ['category' => 'Performance', 'text' => 'Pagination with OFFSET 10000 still reads and discards 10000 rows. Cursor-based pagination (WHERE id > last_seen_id LIMIT 20) skips nothing — it seeks directly. Use it for large datasets.'],
        ['category' => 'Performance', 'text' => 'N+1 in disguise: $order->items->first()->product->name. Looks like one access, but if inside a loop over 50 orders, that\'s 50 product queries. Always think: "What SQL does this line generate?"'],
        ['category' => 'Performance', 'text' => 'String concatenation in a loop ($str .= $chunk) copies the entire string each time. For 10MB of chunks, that\'s O(n²) memory copies. Use an array + implode(), or output buffering.'],

        // Git / DevOps — things that make you reliable
        ['category' => 'Git', 'text' => 'git rebase -i HEAD~5 lets you squash, reorder, edit, or drop commits before pushing. Clean history = reviewable PRs. Never rebase shared branches — only your own feature branches.'],
        ['category' => 'Git', 'text' => 'git bisect finds the exact commit that introduced a bug in O(log n) commits. Mark one good commit, one bad commit, and git binary-searches through history. For 1000 commits, ~10 checks.'],
        ['category' => 'DevOps', 'text' => 'A Docker image layer is cached until its instruction or any file it COPYs changes. Put rarely-changing steps (apt-get, composer install) before frequently-changing ones (COPY app code). Builds go from 3min to 15sec.'],

        // Problem Solving — things that make you think differently
        ['category' => 'Problem Solving', 'text' => 'When stuck on a bug: explain it out loud. If you can\'t clearly state what SHOULD happen vs what DOES happen, you don\'t understand the problem yet. The explanation often reveals the gap.'],
        ['category' => 'Problem Solving', 'text' => 'Before optimizing, measure. Before measuring, define what "fast enough" means. A 200ms endpoint that\'s called 10x/day doesn\'t need optimization. A 50ms endpoint called 100,000x/day might.'],
        ['category' => 'Problem Solving', 'text' => 'The best debugging question: "What changed?" New deploy? New data? New traffic pattern? Config change? 90% of production issues trace back to something that changed in the last 24 hours.'],
        ['category' => 'Problem Solving', 'text' => 'Write the test BEFORE the fix. Reproduce the bug as a failing test. Then fix it. Now you have permanent regression protection and proof the fix actually works — not just "it stopped crashing."'],

        // Competitive Programming — patterns that unlock problem solving
        ['category' => 'Algorithms', 'text' => 'Two pointers: when you have a sorted array and need pairs/subarrays with a target sum, use left + right pointers moving inward. O(n) instead of O(n²). Works on: Two Sum (sorted), Container With Most Water, 3Sum.'],
        ['category' => 'Algorithms', 'text' => 'Sliding window: for "find the best subarray of size k" problems, don\'t recompute the sum each time. Add the new element, remove the old one. O(n) instead of O(n·k). Pattern: expand right, shrink left when constraint breaks.'],
        ['category' => 'Algorithms', 'text' => 'Binary search isn\'t just for sorted arrays. Any time you have a monotonic condition (false false false true true true), you can binary search the transition point. Works on: search in rotated array, find peak, capacity problems.'],
        ['category' => 'Algorithms', 'text' => 'HashMap = O(1) lookup. When a brute force is O(n²) because of nested "find" loops, ask: "Can I store seen elements in a hash map?" Two Sum, Group Anagrams, Longest Substring Without Repeating — all HashMap problems.'],
        ['category' => 'Algorithms', 'text' => 'BFS = shortest path in unweighted graphs. DFS = explore all paths / detect cycles. If the question says "minimum moves" or "shortest", reach for BFS. If it says "all paths" or "connected components", use DFS.'],
        ['category' => 'Algorithms', 'text' => 'Dynamic Programming = "optimal substructure + overlapping subproblems." Translation: can I solve this by combining answers to smaller versions of the same question? If yes, define dp[i] clearly, find the recurrence, and fill the table.'],
        ['category' => 'Algorithms', 'text' => 'Greedy works when the locally optimal choice leads to the globally optimal solution. Proof pattern: assume greedy isn\'t optimal → show contradiction. Classic examples: interval scheduling (sort by end time), Huffman coding, activity selection.'],
        ['category' => 'Algorithms', 'text' => 'Stack pattern: "find the next greater/smaller element" → monotonic stack. Push indices, pop when current element breaks the monotonic property. O(n) because each element is pushed and popped at most once.'],
        ['category' => 'Algorithms', 'text' => 'Prefix sum: if you need range sum queries, precompute prefix[i] = sum of arr[0..i]. Then sum(l, r) = prefix[r] - prefix[l-1]. O(1) per query instead of O(n). Essential for subarray sum problems.'],
        ['category' => 'Algorithms', 'text' => 'Backtracking = DFS with pruning. Build the solution incrementally, abandon a path as soon as it can\'t lead to a valid solution. Template: choose → explore → unchoose. Classic: N-Queens, Sudoku, permutations, combinations.'],
        ['category' => 'Algorithms', 'text' => 'Union-Find (Disjoint Set): group elements into sets with near-O(1) union and find. Use path compression + union by rank. Perfect for: connected components, detect cycle in undirected graph, Kruskal\'s MST.'],
        ['category' => 'Algorithms', 'text' => 'Topological sort: for "ordering tasks with dependencies" problems. Use Kahn\'s algorithm (BFS with in-degree tracking). If the result has fewer nodes than the graph, there\'s a cycle. Works on: course schedule, build order.'],
        ['category' => 'Algorithms', 'text' => 'Bit manipulation shortcuts: n & (n-1) removes the lowest set bit (check power of 2). n & (-n) isolates the lowest set bit. XOR of all elements finds the unique one when others appear twice. These turn O(n) space into O(1).'],
        ['category' => 'Algorithms', 'text' => 'Heap (priority queue): when you need the "k largest" or "k smallest" elements, use a min-heap of size k. Push each element, pop if size > k. O(n log k) instead of O(n log n). Also essential for merge k sorted lists.'],
        ['category' => 'Algorithms', 'text' => 'Trie (prefix tree): for "search words by prefix" problems. Each node = one character, children = next characters. Insert/search in O(word length). Use for: autocomplete, word search, longest common prefix.'],
        ['category' => 'Algorithms', 'text' => 'The complexity cheat sheet: O(1) hash lookup, O(log n) binary search, O(n) linear scan, O(n log n) sorting, O(n²) nested loops, O(2ⁿ) subsets, O(n!) permutations. If n ≤ 20 → brute force works. n ≤ 10⁵ → need O(n log n) or better.'],
        ['category' => 'Algorithms', 'text' => 'Graph problem recognition: "shortest path with weights" → Dijkstra. "Negative weights" → Bellman-Ford. "All pairs shortest path" → Floyd-Warshall. "Minimum connections" → MST (Prim/Kruskal). Match the tool to the constraint.'],
        ['category' => 'Algorithms', 'text' => 'Interval problems pattern: sort by start time. Then scan left to right: if current.start < prev.end → overlap (merge or count). If not → new group. Works on: merge intervals, meeting rooms, insert interval, non-overlapping intervals.'],
        ['category' => 'Algorithms', 'text' => 'Linked list tricks: use a dummy head node to avoid edge cases with head insertion/deletion. Fast + slow pointer finds the middle (slow at middle when fast hits end). Reverse in-place with three pointers: prev, curr, next.'],
        ['category' => 'Algorithms', 'text' => 'Tree traversals: Inorder (left-root-right) gives sorted order in BST. Preorder (root-left-right) = serialization. Postorder (left-right-root) = delete/evaluate. Level-order (BFS) = anything "by level." Pick the right one for the problem.'],
        ['category' => 'Algorithms', 'text' => 'When you see "subarray" → think prefix sum or sliding window. "Subsequence" → think DP or two pointers. "Subset" → think backtracking or bitmask. "Permutation" → think backtracking with visited array. The problem type tells you the pattern.'],
    ];

    public function getTodayFact(): array
    {
        $dayOfYear = now()->dayOfYear;
        $index = $dayOfYear % count($this->facts);
        return $this->facts[$index];
    }
}
