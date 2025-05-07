#!/bin/sh

# Check if the system is Linux
check_system() {
    if [ "$(uname)" != "Linux" ]; then
        echo "Error: This script only supports Linux systems." >&2
        exit 1
    fi
}

# Get CPU cores count
get_cpu_cores() {
    nproc
}

# Get available RAM in MB
get_available_ram() {
    free -m | awk '/^Mem:/ {print $7}'
}

# Get memory already used by PHP-FPM in MB
get_php_fpm_memory() {
    ps -eo comm,rss | awk '$1 ~ /^php-fpm/ {sum+=$2} END {printf "%.0f\n", sum/1024}'
}

# Get average PHP-FPM worker memory usage in MB
get_avg_worker_usage() {
    avg=$(ps aux | awk '
        /php-fpm: pool/ && !/awk/ {sum += $6; count++}
        END {print (count > 0 ? sum / count / 1024 : 0)}')
    
    # Ensure we return at least 1
    echo $(( $(printf "%.0f" "$avg") > 0 ? $(printf "%.0f" "$avg") : 1 ))
}

# Calculate optimal values based on system resources
calculate_optimal_values() {
    local cpu_cores=$1
    local available_ram=$2
    local avg_worker_usage=$3
    
    # Calculate reserve RAM (10% of available RAM)
    local reserve_ram=$(echo "scale=0; $available_ram * 0.1" | bc | sed 's/\..*$//')
    
    # Calculate max_children
    # Formula: floor((available_ram - reserve_ram) / avg_worker_usage / 10) * 10
    local max_children=$(echo "scale=0; (($available_ram - $reserve_ram) / $avg_worker_usage / 10) * 10" | bc | sed 's/\..*$//')
    
    # Calculate start_servers
    # Formula: min(round(max_children * 0.25), cpu_cores * 4)
    local start_servers_1=$(echo "scale=0; $max_children * 0.25" | bc | sed 's/\..*$//')
    local start_servers_2=$(echo "scale=0; $cpu_cores * 4" | bc | sed 's/\..*$//')
    
    # Convert to integers and compare
    start_servers_1=${start_servers_1:-0}
    start_servers_2=${start_servers_2:-0}
    local start_servers=$(( start_servers_1 < start_servers_2 ? start_servers_1 : start_servers_2 ))
    
    # Calculate min_spare_servers
    # Formula: min(round(max_children * 0.25), cpu_cores * 2)
    local min_spare_servers_1=$(echo "scale=0; $max_children * 0.25" | bc | sed 's/\..*$//')
    local min_spare_servers_2=$(echo "scale=0; $cpu_cores * 2" | bc | sed 's/\..*$//')
    
    # Convert to integers and compare
    min_spare_servers_1=${min_spare_servers_1:-0}
    min_spare_servers_2=${min_spare_servers_2:-0}
    local min_spare_servers=$(( min_spare_servers_1 < min_spare_servers_2 ? min_spare_servers_1 : min_spare_servers_2 ))
    
    # Calculate max_spare_servers
    # Formula: min(round(max_children * 0.75), cpu_cores * 4)
    local max_spare_servers_1=$(echo "scale=0; $max_children * 0.75" | bc | sed 's/\..*$//')
    local max_spare_servers_2=$(echo "scale=0; $cpu_cores * 4" | bc | sed 's/\..*$//')
    
    # Convert to integers and compare
    max_spare_servers_1=${max_spare_servers_1:-0}
    max_spare_servers_2=${max_spare_servers_2:-0}
    local max_spare_servers=$(( max_spare_servers_1 < max_spare_servers_2 ? max_spare_servers_1 : max_spare_servers_2 ))
    
    # Ensure all values are at least 1
    max_children=$(( max_children > 0 ? max_children : 1 ))
    start_servers=$(( start_servers > 0 ? start_servers : 1 ))
    min_spare_servers=$(( min_spare_servers > 0 ? min_spare_servers : 1 ))
    max_spare_servers=$(( max_spare_servers > 0 ? max_spare_servers : 1 ))
    
    # Return values as JSON
    echo "{"
    echo "\"max_children\": $max_children,"
    echo "\"start_servers\": $start_servers,"
    echo "\"min_spare_servers\": $min_spare_servers,"
    echo "\"max_spare_servers\": $max_spare_servers"
    echo "}"
}

# Print usage information
print_usage() {
    echo "Usage: $0 [OPTIONS]"
    echo "Options:"
    echo "  -j, --json     Output unformatted JSON"
    echo "  -h, --help     Display this help message"
    echo ""
    echo "Supported Systems:"
    echo "  - Linux only (requires commands: nproc, free, ps, awk, bc)"
    echo ""
    echo "Without options, the script outputs human-readable text."
}

# Main function to get all values
get_config_and_load_values() {
    # First check if the system is supported
    check_system
    
    cpu_cores=$(get_cpu_cores)
    available_ram=$(get_available_ram)
    php_fpm_memory=$(get_php_fpm_memory)
    
    # Add PHP-FPM memory to available RAM
    total_available_ram=$((available_ram + php_fpm_memory))
    
    avg_worker_usage=$(get_avg_worker_usage)
    
    # Ensure all values are at least 1
    cpu_cores=$(( cpu_cores > 0 ? cpu_cores : 1 ))
    total_available_ram=$(( total_available_ram > 0 ? total_available_ram : 1 ))
    
    # Calculate optimal values
    optimal_values=$(calculate_optimal_values "$cpu_cores" "$total_available_ram" "$avg_worker_usage")
    
    # Create the full JSON
    echo "{"
    echo "\"system\": {"
    echo "\"cpu_cores\": $cpu_cores,"
    echo "\"available_ram\": $total_available_ram,"
    echo "\"avg_worker_usage\": $avg_worker_usage"
    echo "},"
    echo "\"optimal\": $optimal_values"
    echo "}"
}

# Output human-readable text
output_text() {
    local json_data="$1"
    
    # Extract values from JSON using grep and sed
    cpu_cores=$(echo "$json_data" | grep -o '"cpu_cores": [0-9]*' | sed 's/"cpu_cores": //')
    available_ram=$(echo "$json_data" | grep -o '"available_ram": [0-9]*' | sed 's/"available_ram": //')
    avg_worker_usage=$(echo "$json_data" | grep -o '"avg_worker_usage": [0-9]*' | sed 's/"avg_worker_usage": //')
    
    max_children=$(echo "$json_data" | grep -o '"max_children": [0-9]*' | sed 's/"max_children": //')
    start_servers=$(echo "$json_data" | grep -o '"start_servers": [0-9]*' | sed 's/"start_servers": //')
    min_spare_servers=$(echo "$json_data" | grep -o '"min_spare_servers": [0-9]*' | sed 's/"min_spare_servers": //')
    max_spare_servers=$(echo "$json_data" | grep -o '"max_spare_servers": [0-9]*' | sed 's/"max_spare_servers": //')
    
    # Output formatted text with blank lines before and after
    echo ""
    echo "System Information:"
    echo "  CPU Cores: $cpu_cores"
    echo "  Available RAM: $available_ram MB"
    echo "  Average Worker Usage: $avg_worker_usage MB"
    echo ""
    echo "Optimal PHP-FPM Configuration:"
    echo "  pm.max_children = $max_children"
    echo "  pm.start_servers = $start_servers"
    echo "  pm.min_spare_servers = $min_spare_servers"
    echo "  pm.max_spare_servers = $max_spare_servers"
    echo ""
}

# Output unformatted JSON
output_json() {
    local json_data="$1"
    # Remove all formatting (spaces, newlines) to create unformatted JSON
    echo "$json_data" | tr -d '\n' | sed 's/ //g'
}

# Parse command line arguments
json_output=0

while [ "$#" -gt 0 ]; do
    case "$1" in
        -j|--json)
            json_output=1
            shift
            ;;
        -h|--help)
            print_usage
            exit 0
            ;;
        *)
            echo "Unknown option: $1" >&2
            print_usage
            exit 1
            ;;
    esac
done

# Check system compatibility first
check_system

# Get the data
json_data=$(get_config_and_load_values)

# Output based on format flag
if [ "$json_output" -eq 1 ]; then
    output_json "$json_data"
else
    output_text "$json_data"
fi