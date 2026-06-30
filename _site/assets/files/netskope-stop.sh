#!/usr/bin/env bash
# vim: ai ts=2 sw=2 et sts=2 ft=sh

# Exit on error unless '|| true'.
#set -o errexit
# Exit on error inside subshells functions.
set -o errtrace
# Do not use undefined variables.
set -o nounset
# Catch errors in piped commands.
set -o pipefail

# Allow empty globs.
shopt -s nullglob

# Separator for expansion.
IFS=$' '

# Globals
# Set variables for current file, directory.
__dir="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
__file="${__dir}/$(basename "${BASH_SOURCE[0]}")"
__base="$(basename "${__file}" .sh)"
__invocation="$(printf %q "${__file}")$( (($#)) && printf ' %q' "$@")"

main ()
{
  local __launchd
  local __daemons_plist
  local __agents_plist
  local __proc_name
  __proc_name="netskope"

  # List launchd services running.
  for __launchd in $(launchctl list | grep -i "$__proc_name" | tail -r | cut -f 3); do
    echo -e "--- Remove: ${__launchd}" 1>&2
    sudo launchctl stop "$__launchd" || true
    sudo launchctl remove "$__launchd" || true
  done

  # List root LaunchDaemons plist files.
  for __daemons_plist in /Library/LaunchDaemons/*"$__proc_name"*; do
    echo -e "--- Unload: ${__daemons_plist}" 1>&2
    sudo launchctl unload "$__daemons_plist" || true
  done

  # List user LaunchAgents plist files.
  for __agents_plist in /Library/LaunchAgents/*"$__proc_name"*; do
    echo -e "--- Unload: ${__agents_plist}" 1>&2
    launchctl unload "$__agents_plist" || true
  done

  for __pid_num in $(sudo ps aux | grep -i "$__proc_name" | grep -v "$__base" | grep -v "grep" | tr -s ' ' | cut -d' ' -f 2); do
    echo -e "--- Kill: $__proc_name [$__pid_num]" 1>&2
    sudo kill -9 "$__pid_num" || true
  done
}

main
