#!/usr/bin/env bash
# PreToolUse: blocks git commit if staged changes contain secrets. Outputs nothing otherwise.

INPUT=$(cat)
COMMAND=$(jq -r '.tool_input.command // empty' <<<"$INPUT")
CWD=$(jq -r '.cwd // empty' <<<"$INPUT")

grep -qE 'git\s+commit' <<<"$COMMAND" || exit 0

ADDED=$(git -C "${CWD:-.}" diff --cached -U0 --diff-filter=ACM -- . ':!*.lock' ':!*.min.*' ':!*.map' 2>/dev/null \
    | grep '^+' | grep -v '^+++')
[[ -z "$ADDED" ]] && exit 0

PATTERNS=(
    'AKIA[0-9A-Z]{16}'
    'sk-[a-zA-Z0-9]{20,}'
    'ghp_[a-zA-Z0-9]{36}'
    'glpat-[a-zA-Z0-9-]{20,}'
    '-----BEGIN (RSA |EC |DSA |OPENSSH )?PRIVATE KEY-----'
    'xox[bprs]-[a-zA-Z0-9-]{10,}'
)

FOUND=()
for p in "${PATTERNS[@]}"; do
    grep -qE -e "$p" <<<"$ADDED" && FOUND+=("$p")
done
[[ ${#FOUND[@]} -eq 0 ]] && exit 0

jq -n --arg r "Possible secrets in staged changes (patterns: ${FOUND[*]}). Remove them before committing." \
    '{hookSpecificOutput: {hookEventName: "PreToolUse", permissionDecision: "deny", permissionDecisionReason: $r}}'
exit 0
