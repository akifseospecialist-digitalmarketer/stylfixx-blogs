
#!/usr/bin/env python3
"""
content/posts/*.json check karta hai:
  - status = scheduled aur date <= abhi  ->  status = published
  - status field hai hi nahi (purane posts): draft true ho to "draft", warna "published"
Sirf jo file badalni hai wahi likhi jati hai.
"""
import json
import glob
from datetime import datetime, timezone, timedelta

IST = timezone(timedelta(hours=5, minutes=30))
now = datetime.now(IST)


def parse_date(value):
    try:
        dt = datetime.fromisoformat(str(value).replace("Z", "+00:00"))
    except Exception:
        return None
    if dt.tzinfo is None:          # purane posts: sirf date, IST maan lo
        dt = dt.replace(tzinfo=IST)
    return dt


changed = []

for path in sorted(glob.glob("content/posts/*.json")):
    with open(path, encoding="utf-8") as f:
        raw = f.read()
    try:
        data = json.loads(raw)
    except Exception:
        print("skip (invalid json):", path)
        continue
    if not isinstance(data, dict):
        continue

    status = data.get("status")
    new_status = None

    if not status:
        new_status = "draft" if data.get("draft") else "published"
    elif status == "scheduled":
        dt = parse_date(data.get("date"))
        if dt is not None and dt <= now:
            new_status = "published"

    if new_status:
        data["status"] = new_status
        # purana draft toggle ab zaroori nahi
        data.pop("draft", None)
        with open(path, "w", encoding="utf-8") as f:
            f.write(json.dumps(data, indent=2, ensure_ascii=False))
        changed.append(f"{path}: -> {new_status}")

if changed:
    print("Updated:")
    print("\n".join(changed))
else:
    print("Nothing to update.")
