#!/bin/bash
set -e

source /gh-toolkit/shell.sh

gh_log ""

CONFIRM_AND_PUSH=$(gh_input "CONFIRM_AND_PUSH")

COMMITTER_NAME=$(gh_input "COMMITTER_NAME")
COMMITTER_EMAIL=$(gh_input "COMMITTER_EMAIL")

gitconfig "$COMMITTER_NAME" "$COMMITTER_EMAIL"

gh_validate_input "FILES" "FILES List is required"

mkdir -p /dynamic-readme-tmp/repos/

if [ -z "$GITHUB_TOKEN" ]; then
  gh_log_error "🚩 Set the GITHUB_TOKEN env variable"
fi

if [ -z "$REPOSITORY_SLUG" ]; then
  gh_log "ℹ︎ Please Use https://github.com/varunsridharan/action-repository-meta Action to expose useful variables"
fi

RAW_FILES=$(gh_input "FILES")
FILES=($RAW_FILES)

GIT_URL="https://x-access-token:${GITHUB_TOKEN}@github.com/${GITHUB_REPOSITORY}.git"

php /dynamic-readme/global-repo.php

gh_log ""

for FILE in "${FILES[@]}"; do
  FILE=($(echo $FILE | tr "=" "\n"))
  SRC_FILE=${FILE[0]}
  gh_log_group_start "📓  ${SRC_FILE}"
  if [ ${FILE[1]+yes} ]; then
    DEST_FILE="${FILE[1]}"
  else
    DEST_FILE="${SRC_FILE}"
  fi

  DEST_FOLDER_PATH=$(dirname "${GITHUB_WORKSPACE}/${DEST_FILE}")

  if [ ! -d "$DEST_FOLDER_PATH" ]; then
    gh_log "  Creating [$DEST_FOLDER_PATH]"
    mkdir -p $DEST_FOLDER_PATH
  fi

  gh_log "SRC_FILE : ${SRC_FILE}"
  gh_log "DEST_FILE : ${DEST_FILE}"

  php /dynamic-readme/app.php "${SRC_FILE}" "${DEST_FILE}"
  gh_log ""

  if [ "$CONFIRM_AND_PUSH" ]; then
    gh_log "🚀 Confirm and push is the strategy used"

    git add "${GITHUB_WORKSPACE}/${DEST_FILE}" -f

    if [ "$(git status --porcelain)" != "" ]; then
      COMMIT_MESSAGE=$(gh_input "COMMIT_MESSAGE")
      git commit -m "$COMMIT_MESSAGE"
    else
      gh_log "  ✅ No Changes Are Done : ${SRC_FILE}"
    fi
  else
    git add -A
  fi

  gh_log_group_end
done
gh_log ""

if [ "$CONFIRM_AND_PUSH" ]; then
  git push $GIT_URL
fi

gh_log ""