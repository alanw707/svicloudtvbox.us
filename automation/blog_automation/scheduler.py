#!/usr/bin/env python3
from __future__ import annotations

import argparse
import datetime as dt
import os
import subprocess
import sys
import time
from pathlib import Path
from typing import List, Tuple

import yaml
from zoneinfo import ZoneInfo

WEEKDAY_MAP = {
    "monday": 0,
    "tuesday": 1,
    "wednesday": 2,
    "thursday": 3,
    "friday": 4,
    "saturday": 5,
    "sunday": 6,
}


def load_schedule(config_path: Path) -> tuple[List[Tuple[int, int, int]], dt.tzinfo]:
    config = yaml.safe_load(config_path.read_text(encoding="utf-8")) or {}
    publishing = config.get("publishing", {})
    schedule_entries = publishing.get("schedule") or []
    if not schedule_entries:
        raise ValueError("No publishing.schedule entries found in config.yaml")

    tz_name = publishing.get("timezone") or os.getenv("TZ") or "UTC"
    try:
        tz: dt.tzinfo = ZoneInfo(str(tz_name))
    except Exception:
        tz = dt.timezone.utc

    schedule: List[Tuple[int, int, int]] = []
    for entry in schedule_entries:
        day_raw = (entry.get("day") or "").strip().lower()
        time_raw = (entry.get("time") or "").strip()
        if day_raw not in WEEKDAY_MAP:
            continue
        try:
            hour, minute = [int(part) for part in time_raw.split(":", 1)]
        except ValueError:
            continue
        schedule.append((WEEKDAY_MAP[day_raw], hour, minute))

    if not schedule:
        raise ValueError("publishing.schedule is empty or invalid")
    return schedule, tz


def next_run(schedule: List[Tuple[int, int, int]], now: dt.datetime) -> dt.datetime:
    candidates = []
    for weekday, hour, minute in schedule:
        days_ahead = (weekday - now.weekday()) % 7
        candidate = (now + dt.timedelta(days=days_ahead)).replace(
            hour=hour, minute=minute, second=0, microsecond=0
        )
        if candidate <= now:
            candidate += dt.timedelta(days=7)
        candidates.append(candidate)
    return min(candidates)


def emergency_stop_active(stop_file: Path) -> bool:
    return stop_file.exists()


def log(message: str) -> None:
    stamp = dt.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    print(f"[{stamp}] {message}", flush=True)


def run_once(args: argparse.Namespace) -> None:
    command = [
        sys.executable,
        str(Path(__file__).with_name("main.py")),
        "--config",
        str(args.config),
        "--env",
        str(args.env),
    ]
    if args.max_posts is not None:
        command.extend(["--max-posts", str(args.max_posts)])
    if args.dry_run:
        command.append("--dry-run")

    log(f"Starting automation run: {' '.join(command)}")
    try:
        subprocess.run(command, check=True)
        log("Automation run finished")
    except subprocess.CalledProcessError as exc:
        log(f"Automation run failed with exit code {exc.returncode}")


def scheduler_loop(args: argparse.Namespace) -> None:
    schedule, tz = load_schedule(args.config)
    stop_file = Path(args.stop_file)
    while True:
        now = dt.datetime.now(tz)
        target = next_run(schedule, now)
        log(f"Next automation run scheduled for {target.isoformat()} ({tz})")
        while True:
            now = dt.datetime.now(tz)
            remaining = (target - now).total_seconds()
            if remaining <= 0:
                break
            time.sleep(min(300, max(1, remaining)))

        if emergency_stop_active(stop_file):
            log(f"Emergency stop file {stop_file} detected. Skipping run and sleeping 10 minutes.")
            time.sleep(600)
            continue

        run_once(args)


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="SVICLOUD blog automation scheduler")
    parser.add_argument("--config", type=Path, default=Path("config.yaml"), help="Path to configuration file")
    parser.add_argument("--env", type=Path, default=Path("../../.env"), help="Path to .env file")
    parser.add_argument("--max-posts", type=int, help="Override max posts per run")
    parser.add_argument("--dry-run", action="store_true", help="Run automation in dry-run mode")
    parser.add_argument("--stop-file", type=Path, default=Path("EMERGENCY_STOP"), help="Emergency stop file path")
    parser.add_argument("--run-once", action="store_true", help="Execute one run immediately and exit")
    return parser.parse_args()


def main() -> None:
    args = parse_args()
    if args.run_once:
        run_once(args)
    else:
        log("Scheduler started. Press Ctrl+C to stop.")
        try:
            scheduler_loop(args)
        except KeyboardInterrupt:
            log("Scheduler stopped by user")


if __name__ == "__main__":
    main()
